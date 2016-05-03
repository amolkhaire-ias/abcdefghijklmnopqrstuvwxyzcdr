<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocStageMap extends Model
{
    //
    protected $table = 'docstagemaps';
    public function companyDocument() {
        return $this->hasOne('App\Models\CompanyDocument','id','docid');
    }
    public static function createDocStageMap($cmpid, $pkgid, $cmpdocid, $stageid) {
        $docstagemaps = new DocStageMap();
        $docstagemaps->companyid = $cmpid;
        $docstagemaps->packageid = $pkgid;
        $docstagemaps->docid = $cmpdocid;
        $docstagemaps->stageid = $stageid;
        $docstagemaps->save();
    }
    public static function getStageDoclist($cmpid, $pkgid) {
        $docstagemaps = DocStageMap::where('companyid', '=', $cmpid)
        ->where('packageid', '=', $pkgid)
        ->get();
        return $docstagemaps;
    }

    public static function getDocidByStageid($stageid) {
        $docstagemaps = DocStageMap::select('docid')
            ->where('stageid', '=', $stageid)
            ->get();
        return $docstagemaps;
    }
    public static function getDocStageMap($stageid) {
        $docstagemaps = DocStageMap::where('stageid', '=', $stageid)
            ->get();
        return $docstagemaps;
    }
}
