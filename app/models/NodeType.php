<?php

class NodeType extends BaseModel {

    public function collections()
    {
        return $this->belongsToMany('Collection');
    }
    
    public static function categorySelect()
    {
        $return = array();

        foreach (Config::get('node-categories') as $category) {
            $return[ $category['name'] ] = $category['label'];
        }

        return $return;
    }

    public function getColumnsAttribute($columns)
    {
        return json_decode($columns);
    }

    public function setColumnsAttribute($columns)
    {
        $processedColumns = array();

        // We need to do a bit of processing here...
        if (count($columns)) {
            foreach ($columns as $column) {

                if (!isset($column['name'])) {
                    $column['name'] = Str::slug($column['label']);
                }

                if (isset($column['values'])) {
                    $column['values'] = array_filter($column['values']);
                }

                $processedColumns[] = $column;
            }
        }

        $this->attributes['columns'] = json_encode($processedColumns);
    }

    public function tableName()
    {
        return 'node_type_' . $this->getAttribute('id');
    }

    public function createTable()
    {
        $categories = Config::get('node-categories');
        $nodetype = $this;

        Schema::create($this->tableName(), function($table) use ($categories, $nodetype) {
            $table->increments('id');
            $table->integer('node_id')->index();
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->enum('status', array('draft', 'awaiting-approval', 'published', 'retired', 'for-review'))->default('draft');

            foreach ($nodetype->columns as $column) {
                $col_type = $categories[ $column->category ]['type'];

                switch ($col_type) {
                    case 'enum':

                        $table->enum( $column->name, $column->values )->nullable();

                        break;

                    default:

                        $table->$col_type( $column->name )->nullable();

                        break;
                }
            }

            $table->timestamps();
        });

        return tableExists($this->tableName());
    }

    /**
     * Returns the admin view for a specified category
     *
     * @param  string $category category name
     * @param  array $data     optional data
     *
     * @return string           template string
     */
    public static function viewForCategory($category, $data = null)
    {
        $category_config = Config::get('node-categories.' . $category);

        return View::make('nodecategories.admin.' . $category_config['admin'], array('category' => $category_config, 'data' => $data));
    }

}