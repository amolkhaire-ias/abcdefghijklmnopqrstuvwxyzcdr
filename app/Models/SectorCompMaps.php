<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SectorCompMaps extends Model {
    protected $table = 'sectorcompmaps';

    public static function storeMultiSector($newsectorids, $companyid, $oldcompanyid = null) {
        $deloldseccmpmaps = SectorCompMaps::select('id')
            ->where('companyid', '=', $companyid)
            ->get();
        if($deloldseccmpmaps) {
            foreach($deloldseccmpmaps as $deloldseccmpmap) {
                $Seccmpmap = SectorCompMaps::find($deloldseccmpmap->id);
                $Seccmpmap->delete();
            }
        }

        //$sectorids is new list of sector id
        $oldsectorids = SectorCompMaps::where('companyid', '=', $oldcompanyid)
            ->where('active', '=', 1)
            ->where('approved', '=', 1)
            ->lists('sectorid', 'sectorid');
        //$oldsectorids is old list of sector id
        foreach($newsectorids as $sectorid) {
            $secidfound = 0;
            $sectorcompmaps = new SectorCompMaps();
            if($oldcompanyid) {
                $secidfound = isset($oldsectorids[$sectorid]) ? 1 : 0;
                if($secidfound) {
                    $seccompmapid = $sectorcompmaps->select('id')
                        ->where('companyid', '=', $oldcompanyid)
                        ->where('sectorid', '=', $sectorid)
                        ->where('active', '=', 1)
                        ->where('approved', '=', 1)
                        ->first();
                    $sectorcompmaps->oid = $seccompmapid->id;
                }
            }
                $sectorcompmaps->companyid = $companyid;
                $sectorcompmaps->action = $secidfound == 0 ? 'c' : 'u';
                $sectorcompmaps->sectorid = $sectorid;
                $sectorcompmaps->active = '0';
                $sectorcompmaps->approved = '0';
                $sectorcompmaps->save();
        }
        foreach($oldsectorids as $oldsectorid) {
            $sectorcompmaps = new SectorCompMaps();
            $found = 0;
            foreach($newsectorids as $newsectorid) {
                if($oldsectorid == $newsectorid) {
                    $found = 1;
                }
            }
            if($found == 0) {
                $delseccompmapid = $sectorcompmaps->select('id')
                    ->where('companyid', '=', $oldcompanyid)
                    ->where('sectorid', '=', $oldsectorid)
                    ->where('active', '=', 1)
                    ->where('approved', '=', 1)
                    ->first();
                $sectorcompmaps->companyid = $companyid;
                $sectorcompmaps->oid = $delseccompmapid->id;
                $sectorcompmaps->sectorid = $oldsectorid;
                $sectorcompmaps->action = 'd';
                $sectorcompmaps->active = '0';
                $sectorcompmaps->approved = '0';
                $sectorcompmaps->save();
            }
        }
    }
    public static function approvePendingSecCompMaps($cmpid, $cmpoid) {
        $pendseccmpmapids = SectorCompMaps::select('id')
            ->where('companyid', '=', $cmpid)
            ->where('active', '=', 0)
            ->where('approved', '=', 0)
            ->get();
        foreach($pendseccmpmapids as $pendseccmpmapid) {
            $pendseccmpmaps = SectorCompMaps::find($pendseccmpmapid->id);
            $oid = $pendseccmpmaps->oid;
            $action = $pendseccmpmaps->action;
            if($oid == 0 && $action == 'c') {
                if($cmpoid > 0) {
                    $pendseccmpmaps->companyid = $cmpoid;
                }
                $pendseccmpmaps->active = '1';
                $pendseccmpmaps->approved = '1';
                $pendseccmpmaps->save();
            }else if($oid > 0 && $action == 'd') {
                $oldseccmpmaps = SectorCompMaps::find($oid);
                $oldseccmpmaps->active = '0';
                $oldseccmpmaps->action = '';
                $oldseccmpmaps->save();
                $pendseccmpmaps->delete();
            }else if($oid > 0 && $action == 'u') {
                $oldseccmpmaps = SectorCompMaps::find($oid);
                $oldseccmpmaps->active = '1';
                $oldseccmpmaps->approved = '1';
                $oldseccmpmaps->save();
                $pendseccmpmaps->delete();
            }
        }
        if(isset($pendseccmpmaps)) {
            return true;
        }
        return false;
    }
    public static function listCmpidBySectId($cmpid, $pendview = null) {
        $seccmpmapsecids = SectorCompMaps::where('companyid', '=', $cmpid)
            ->where('active', '=', $pendview ? 0 : 1)
            ->where('approved', '=', $pendview ? 0 : 1)
            ->where('action', '!=', 'd')
            ->lists('companyid', 'sectorid');
        return $seccmpmapsecids;
    }
    public static  function rejectPendingSecCompMaps($cmpid) {
        $rejectseccmps = SectorCompMaps::select('id')
            ->where('companyid', '=', $cmpid)
            ->get();
        if($rejectseccmps) {
            foreach($rejectseccmps as $rejectseccmp) {
                $sectcmpmap = SectorCompMaps::find($rejectseccmp->id);
                $sectcmpmap->active = 1;
                $sectcmpmap->approved = 0;
                $sectcmpmap->save();
            }
        }
    }
}
