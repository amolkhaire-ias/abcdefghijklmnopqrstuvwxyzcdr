<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class CompanyDocument extends Model
{
    //
    protected $table = 'companydocuments';
    public static function createCmpDocs($cmpid, $pkgid, $alldata,$stagenum,$tltype) {
        if(isset($alldata['doclink-s' . $stagenum])) {
            $companydocuments = new CompanyDocument();
            $companydocuments->companyid = $cmpid;
            $companydocuments->packageid = $pkgid;
            if(isset($alldata['doctype-s'.$stagenum])) {
                $companydocuments->doctype = $alldata['doctype-s' . $stagenum];
            }
            if(isset($alldata['doclink-s'.$stagenum])) {
                $companydocuments->uploaddate = Carbon::now();
                $file = $alldata['doclink-s' . $stagenum];
                $destinationPath = 'uploads/timeline/' . $cmpid . '/' . $tltype . '/' . $stagenum;
                $filename = $file->getClientOriginalName();
                $upload_success = $file->move($destinationPath, $filename);
                if($upload_success) {
                    $companydocuments->doclink = $filename;
                }
            }
            if(isset($alldata['docname-s'.$stagenum])) {
                $companydocuments->docname = $alldata['docname-s'.$stagenum];
            }

            if(isset($alldata['docdescription-s'.$stagenum])) {
                $companydocuments->docdescription = $alldata['docdescription-s'.$stagenum];
            }
            $companydocuments->save();
            return $companydocuments->id;
        }
    }

    public static function createTimelineDocs($cmpid, $pkgid, $alldata,$stagenum) {
           if(isset($alldata['doclink-s'.$stagenum])) {
               $companydocuments = new CompanyDocument();
               $companydocuments->companyid = $cmpid;
               $companydocuments->packageid = $pkgid;
               if(isset($alldata['doctype-s'.$stagenum])) {
                   $companydocuments->doctype = $alldata['doctype-s'.$stagenum];
               }
               if(isset($alldata['doclink-s'.$stagenum])) {
                   $companydocuments->uploaddate = Carbon::now();
                   $file = $alldata['doclink-s'.$stagenum];
                   $destinationPath = 'uploads/timeline/' . $cmpid . '/' . $alldata['id'];
                   $filename = $file->getClientOriginalName();
                   $upload_success = $file->move($destinationPath, $filename);
                   if($upload_success) {
                       $companydocuments->doclink = $filename;
                   }
               }
               if(isset($alldata['docname-s'.$stagenum])) {
                   $companydocuments->docname = $alldata['docname-s'.$stagenum];
               }

               if(isset($alldata['docdescription-s'.$stagenum])) {
                   $companydocuments->docdescription = $alldata['docdescription-s'.$stagenum];
               }
               $companydocuments->save();
               return $companydocuments->id;
           }
    }
}
