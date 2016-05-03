<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RelationView extends Model
{

    protected $table= 'relationview';

    public static function checkrelation($tablename, $id)
    {
            $relations = RelationView::where('parent',$tablename)->get()->toArray();
            if(count($relations) == 0){
                return false;
            }else{
                foreach($relations as $relation){
                    $childtable = $relation['child'];
                    $childcolumn = $relation['name'];
                    $data = DB::select(DB::raw("SELECT * FROM $childtable WHERE $childcolumn = $id"));
                    if(count($data) > 0){
                        return false;
                    }else{
                        return true;
                    }
                }
            }
    }
}
