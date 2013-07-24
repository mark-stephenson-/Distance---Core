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

    public function checkRequiredColumns($post_data)
    {
        $columns = $this->requiredColumns();
        $errors = array();

        if (count($columns)) {
            foreach($columns as $column) {

                if (empty($post_data[$column->name])) {
                    $errors[] = $column->label;
                }

            }
        }

        return $errors;
    }

    public function requiredColumns()
    {
        $ret = array();

        if (count($this->getAttribute('columns'))) {
            foreach($this->getAttribute('columns') as $column) {
                if (isset($column->required) and $column->required) {
                    $col = new stdClass;
                    $col->label = $column->label;
                    $col->name = $column->name;
                    $ret[] = $col;
                }
            }
        }

        return $ret;
    }

    public function parseColumns($post_data)
    {
        $columns = $this->getAttribute('columns');

        if (count($columns) > 0) {
            foreach ($post_data as $key => &$val) {

                $column_obj = findObjectInArray($columns, $key, 'name');

                switch ($column_obj->category) {
                    case 'date':
                        $d = str_replace('/', '-', $val);
                        $stamp = strtotime($d);
                        $val = date('Y-m-d H:i:s', $stamp);
                        break;

                    case 'html':
                        $val = stripslashes($val);
                        break;

                    case 'enum-multi':
                        if (is_array($val)) {
                            $val = implode(', ', $val);
                        }
                        break;
                    case 'nodelookup':
                        $val = (int) $val;
                        break;
                }

            }
        }

        return $post_data;
    }

    /**
     * returns an array of id => node type label
     *
     * @param  Collection $collection   The associated collection
     * @param  boolean $withExisting    Include an option for an existing node type
     *
     * @return array                    List of node type labels
     */
    public static function forSelect(Collection $collection = null, $withExisting = false)
    {
        $types = ($collection) ? $collection->nodetypes() : self;

        $return = $types->select(array('*', 'node_types.id as node_types_id'))
                        ->orderBy('label', 'ASC')
                        ->lists('label', 'node_types_id');
        
        if ($withExisting) {
            $return['existing'] = "Existing Item";
        }

        return $return;
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