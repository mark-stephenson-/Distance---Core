<?php

class Resource extends BaseModel
{

    protected $softDelete = true;

    public function catalogue()
    {
        return $this->belongsTo('Catalogue');
    }

    public function getDisplayTextAttribute()
    {
        return ($this->getAttribute('description')) ?: $this->getAttribute('filename');
    }

    public function path()
    {
        return route('resources.load', array($this->getAttribute('collection_id'), $this->getAttribute('filename')));
    }

    public function systemPath()
    {
        return app_path() . '/../resources/' . $this->getAttribute('collection_id') . '/' . $this->getAttribute('filename');
    }

    public function isPdf()
    {
        // The quickest way, check the extensions
        if (in_array($this->ext, array('pdf'))) {
            return true;
        }

        $filePath = $this->systemPath();

        // The quick method...
        try {
            $file = new Symfony\Component\HttpFoundation\File\File($filePath);
        }
        catch(Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException $e) {
            return false;
        }

        if (in_array($file->getMimeType(), array('application/pdf'))) {
            return true;
        }

        return false;
    }

    public function isImage()
    {
        // The quickest way, check the extensions
        if (in_array($this->ext, array('jpg', 'jpeg', 'png', 'gif'))) {
            return true;
        }

        $filePath = $this->systemPath();

        // The quick method...
        try {
            $file = new Symfony\Component\HttpFoundation\File\File($filePath);
        }
        catch(Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException $e) {
            return false;
        }

        if (in_array($file->getMimeType(), array('image/jpg', 'image/jpeg', 'image/png', 'image/gif'))) {
            return true;
        }

        return false;
    }

    public static function fetch($collectionId, $fileName, $downloadFile = false)
    {

        if (strpos($fileName, '_id') !== false) {
            $resourceId = str_replace('_id', '', $fileName);

            if (is_numeric($resourceId)) {

                $resource = Cache::remember(
                    'resourceId_' . $resourceId,
                    60 * 60 * 24,
                    function () use ($resourceId) {
                        return Resource::find($resourceId);
                    }
                );

                $fileName = $resource->filename;
            }
        }

        $type = Input::get('type') . '/';

        // We don't need type for now
        $type = '/';

        $filePath = app_path() . '/../resources/' . $collectionId . '/' . $fileName;

        if (!file_exists($filePath)) {

            return Response::make('', 404);

            // Try without the type
            $typePath = $filePath;
            $filePath = app_path() . '/../resources/' . $fileName;

            if (file_exists($filePath)) {
                // We have the original, let's re-create the types

                $uploadPath = app_path() . '/../resources/';

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

                if (file_exists($typePath)) {
                    $filePath = $typePath;
                }
            }
        }

        if ($downloadFile) {
            return Response::download($filePath);
        }

        $file = new Symfony\Component\HttpFoundation\File\File($filePath);

        return Response::make(
            File::get($filePath), 200, array(
                'Content-Type' => $file->getMimeType()
            )
        );
    }

}