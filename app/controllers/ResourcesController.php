<?php

class ResourcesController extends BaseController
{
    
    public function load($catalogueId, $language, $fileName)
    {
        // Check if it's public        
        if (!Sentry::check()) {
            $resource = Resource::where('filename', '=', $fileName)
                                ->where('catalogue_id', '=', $catalogueId)
                                ->first();

            if (!$resource) {
                return Response::make('', 404);
            }

            if (!$resource->public) {
                return Response::make('', 403);
            }
        }
        
        return Resource::fetch($catalogueId, $language, urldecode($fileName));
    }

    public function index() {
        $catalogues = Collection::current()->catalogues()->with('resources')->get();

        return View::make('resources.index', compact('catalogues'));
    }

    public function show($appId, $collectionId, $catalogueId) {
        $catalogue = Catalogue::with('resources')->findOrFail($catalogueId);
        $collection = Collection::current();

        return View::make('resources.show', compact('catalogue', 'collection'));
    }

    public function create() {
        $catalogue = new Catalogue;
        $collections = Collection::get();

        return View::make('catalogues.form', compact('catalogue', 'collections'));
    }

    public function store() {
        // Let's run the validator
        $validator = new Core\Validators\Catalogue;

        // If the validator fails
        if ($validator->fails()) {
            return Redirect::back()
                ->withInput()
                ->withErrors($validator->messages());
        }

        $catalogue = new Catalogue;

        $catalogue->name = Input::get('name');
        $catalogue->restrictions = array_filter(explode(',', trim(Input::get('restrictions', ''))));

        $catalogue->save();

        $catalogue->collections()->sync(Input::get('collections', array()));

        return Redirect::route('catalogues.index', array(CORE_APP_ID, CORE_COLLECTION_ID))
                ->with('successes', new MessageBag(array($catalogue->name . ' has been created.')));
    }

    public function edit($appId, $collectionId, $catalogueId) {
        $catalogue = Catalogue::with('collections')->findOrFail($catalogueId);
        $collections = Collection::get();

        return View::make('catalogues.form', compact('catalogue', 'collections'));
    }

    public function update($appId, $collectionId, $catalogueId) {

        $catalogue = Catalogue::findOrFail($catalogueId);

        // Let's run the validator
        $validator = new Core\Validators\Catalogue;

        // If the validator fails
        if ($validator->fails()) {
            return Redirect::back()
                ->withInput()
                ->withErrors($validator->messages());
        }

        $catalogue->name = Input::get('name');
        $catalogue->restrictions = array_filter(explode(',', trim(Input::get('restrictions', ''))));
        $catalogue->collections()->sync(Input::get('collections', array()));

        $catalogue->save();


        return Redirect::route('catalogues.index', array(CORE_APP_ID, CORE_COLLECTION_ID))
                ->with('successes', new MessageBag(array($catalogue->name . ' has been updated.')));
    }

    public function process($appId, $collectionId, $catalogueId, $language = "en") {

        $response = array('success' => false);

        $collection = Collection::find($collectionId);

        if (!$collection) {
            $response['msg'] = 'Invalid collection.';
            return json_encode($response);
        }

        $catalogue = Catalogue::find($catalogueId);

        if (!$catalogue) {
            $response['msg'] = 'Invalid catalogue.';
            return json_encode($response);
        }

        $uploadPath = app_path() . '/../resources/' . $catalogueId . '/' . $language;
        $fileUpload = Input::file('file');

        if ($fileUpload->getError() > 0) {

            switch($fileUpload->getError()) {
                case 1:
                case 2:
                    $response['msg'] = 'The file was bigger than the maximum allowed file size.';
                    break;
                case 7:
                    $response['msg'] = 'The destination folder is not set to writable.';
                    break;
                default:
                    $response['msg'] = 'An unknown error occured.';
                    break;
            }

            return json_encode($response);
        }

        $fileName = $fileUpload->getClientOriginalName();

        // Let's replace all spaces with underscores
        $fileName = str_replace(' ', '_', $fileName);

        $fileExt = $fileUpload->getClientOriginalExtension();
        $fileBeforeExt = str_replace('.' . $fileExt, '', $fileName);

        // We don't want to overwrite if the file is already there...
        if (file_exists($uploadPath . $fileName)) {
            // Remove the ext and add a random number (and hope that it doesn't exist!!)
            $rand = substr(md5(time()), 0, 6);
            $fileName = $fileBeforeExt . '-' . $rand . '.' . $fileExt;
            $fileBeforeExt = $fileBeforeExt . '-' . $rand;
        }

        $fileMime = $fileUpload->getMimeType();

        if ($fileUpload->move($uploadPath, $fileName)) {

            $resource = new Resource;
            $resource->filename = $fileName;
            $resource->catalogue_id = $catalogue->id;
            $resource->collection_id = $collection->id;
            $resource->sync = 1;
            $resource->mime = $fileMime;

            /*
                No thumbnails for now...
            
            // Now to create the thumbnail versions
            $dimensions = array(
                'view' => new \Imagine\Image\Box(150,100),
                'thumb' => new \Imagine\Image\Box(75,50),
            );

            $imagine = new \Imagine\Gd\Imagine();
            $mode    = \Imagine\Image\ImageInterface::THUMBNAIL_INSET;

            foreach($dimensions as $key => $size) {
                try {
                    $imagine->open($uploadPath . $fileName)
                            ->thumbnail($size, $mode)
                            ->save($uploadPath . '/' . $key . '/' . $fileName);
                } catch(\InvalidArgumentException $e) {
                    // Do nothing as this is probably just a PDF
                }
            }

            */

            if ($resource->save()) {
                
                $i18n_resource = new I18nResource;
                $i18n_resource->resource_id = $resource->id;
                $i18n_resource->lang = $language;
                
                if ($i18n_resource->save()) {
                    $response['data'] = $resource->toArray();
                    $response['success'] = true;
                    return json_encode($response);
                } else {
                    $response['msg'] = 'Failed to save file info to the database.';
                    return json_encode($response);
                }
                
            } else {
                $response['msg'] = 'Failed to save file info to the database.';
                return json_encode($response);
            }
            
        } else {
            $response['msg'] = 'Failed to move file to final destination.';
            return json_encode($response);
        }

    }

    public function destroy($appId, $collectionId, $id, $language) {
        $resource = Resource::find($id);
        $isLastI18n = I18nResource::where('resource_id', $id)->where('lang', $language)->count() == 1;
        $i18n_resource = I18nResource::where('resource_id', $id)->where('lang', $language)->first();
        
        if (!$resource || !$i18n_resource) {
            return Redirect::to('resources')
                ->withErrors( array('That localisation could not be found') );
        }
        
        if (!$i18n_resource->delete()) {
            return Redirect::route('resources.show', array(CORE_APP_ID, CORE_COLLECTION_ID, $resource->catalogue_id, $language))
                ->withErrors( array('That localisation could not be deleted.'));
        }

        // Now we know it's been deleted from the database remove the files
        $folder = base_path() . '/resources/' . $resource->catalogue_id . '/' . $language . '/';

        @unlink($folder . $resource->filename);
        @unlink($folder . 'thumb/' . $resource->filename);
        @unlink($folder . 'view/' . $resource->filename);

        return Redirect::route('resources.show', array(CORE_APP_ID, CORE_COLLECTION_ID, $resource->catalogue_id, $language))
                ->with('successes', new MessageBag( array('That localisation has been deleted.') ));
    }
    
    public function destroyResource($appId, $collectionId, $id, $language) {
        $resource = Resource::find($id);
        $i18n_resources = I18nResource::where('resource_id', $id)->get();

        if (!$resource || !$i18n_resources) {
            return Redirect::to('resources')
            ->withErrors( array('That resource could not be found') );
        }

        if (!$resource->delete()) {
            return Redirect::route('resources.show', array(CORE_APP_ID, CORE_COLLECTION_ID, $resource->catalogue_id, $language))
            ->withErrors( array('That resource could not be deleted.'));
        } else {
            foreach($i18n_resources as $i18n_resource) {
                if (!$i18n_resource->delete()) {
                    return Redirect::route('resources.show', array(CORE_APP_ID, CORE_COLLECTION_ID, $resource->catalogue_id, $language))
                        ->withErrors( array('That resource could not be deleted.'));
                } else {
                    $folder = base_path() . '/resources/' . $resource->catalogue_id . '/' . $i18n_resource->lang . '/';

                    @unlink($folder . $resource->filename);
                    @unlink($folder . 'thumb/' . $resource->filename);
                    @unlink($folder . 'view/' . $resource->filename);
                }
            }
        }
        return Redirect::route('resources.show', array(CORE_APP_ID, CORE_COLLECTION_ID, $resource->catalogue_id, $language))
                ->with('successes', new MessageBag( array("That resource and it's localisations have been deleted.") ));
    }
    
    public function editName($appId, $collectionId, $id, $language) {
        $resource = Resource::whereId($id)->first();
        $language = Input::get('language') ?: $language;
        
        if (!$resource) {
            return Redirect::to('resources')
                ->withErrors( array('That resource could not be found') );
        }
        
        //Store file names in variables
        $oldFileName = $resource->filename;
        $newFileName = Input::get('file-name');
        
        //Rename the resources name in the database
        $resource->filename = $newFileName;
        $resource->save();
        
        //Retrieve all localisations linked with the resource
        $i18n_resources = I18nResource::where('resource_id', $id)->get(array('lang'));
        
        if ($i18n_resources) {
            foreach($i18n_resources as $i18n_resource) {
                $uploadPath = app_path().'/../resources/'.$resource->catalogue_id.'/'.$i18n_resource->lang.'/';

                if (File::move($uploadPath.'/'.$oldFileName, $uploadPath.'/'.$newFileName)) {
                    // Clear out the cached versions
                    @unlink($uploadPath.'thumb/'.$oldFileName);
                    @unlink($uploadPath.'view/'.$oldFileName);

                    $resource->touch();

                    Artisan::call('core:createResourceArchive', array('collection-id' => CORE_COLLECTION_ID));
                } else {
                    return Redirect::route('resources.show', array(CORE_APP_ID, CORE_COLLECTION_ID, $resource->catalogue_id, $language))
                        ->withErrors(array('There was an unexpected issue that has prevented the resource being renamed. Please try again.'));
                }
            }
            Log::debug("editName", array("i18n_resources" => $i18n_resources));
            return Redirect::route('resources.show', array(CORE_APP_ID, CORE_COLLECTION_ID, $resource->catalogue_id, $language))
            ->with('success', new MessageBag(array('Resource has been renamed to ' . $resource->filename . '.')));
        } else {
            return Redirect::to('resources')
                ->withErrors(array('That resource could not be found'));
        }
    }   
    
    public function updateFile($appId, $collectionId, $id, $language) {
        $resource = Resource::whereId($id)->first();
        $language = Input::get('language');
        
        if (!$resource) {
            return Redirect::to('resources')
                ->withErrors( array('That resource could not be found') );
        }
        
        $i18n_resource = I18nResource::where('resource_id', $id)->where('lang', $language)->first();

        if (!$i18n_resource) {
            $i18n_resource = new I18nResource;
            $i18n_resource->resource_id = $id;
            $i18n_resource->lang = $language;
        }
        
        $i18n_resource->save();
        
        $uploadPath = app_path() . '/../resources/' . $resource->catalogue_id . '/' . $language . '/';
        $fileUpload = Input::file('file');

        if ($fileUpload->getError() > 0) {
            
            switch($fileUpload->getError()) {
                case 1:
                case 2:
                    $response = 'The file was bigger than the maximum allowed file size.';
                    break;
                case 7:
                    $response = 'The destination folder is not set to writable.';
                    break;
                default:
                    $response = 'An unknown error occured.';
                    break;
            }

            return Redirect::route('resources.show', array(CORE_APP_ID, CORE_COLLECTION_ID, $resource->catalogue_id, $language))
                ->withErrors($response);
        }

        if ($fileUpload->move($uploadPath, $resource->filename)) {
            // Clear out the cached versions
            @unlink($uploadPath . 'thumb/' . $resource->filename);
            @unlink($uploadPath . 'view/' . $resource->filename);

            $resource->touch();

            Artisan::call('core:createResourceArchive', array('collection-id' => CORE_COLLECTION_ID));

            return Redirect::route('resources.show', array(CORE_APP_ID, CORE_COLLECTION_ID, $resource->catalogue_id, $language))
                ->with('success', new MessageBag(array('The new version of ' . $resource->filename . ' has been uploaded.')));
        } else {
            return Redirect::route('resources.show', array(CORE_APP_ID, CORE_COLLECTION_ID, $resource->catalogue_id, $language))
                ->withErrors(array('There was an unexpected issue that has prevented your file being uploaded. Please try again.'));
        }
    }

}
