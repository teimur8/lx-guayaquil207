<?php
/**
 * Created by Laximo.
 * User: elnikov.a
 * Date: 17.08.17
 * Time: 14:04
 */

namespace guayaquil\views\unit;

use guayaquil\Config;
use guayaquil\modules\pathway\Pathway;
use guayaquil\guayaquillib\data\Language;
use guayaquil\View;


class UnitHtml extends View
{
    public function Display($tpl = 'unit', $view = 'view')
    {
        $catalogCode = $this->input->get('c');
        $ssd         = $this->input->get('ssd', '');
        $uid         = $this->input->get('uid');
        $vid         = $this->input->get('vid');
        $params      = ['c' => $catalogCode, 'ssd' => $ssd, ''];
        $skipped     = $this->input->get('skipped');

        $requests = [
            'appendGetUnitInfo'        => [
                'uid' => $uid
            ],
            'appendListDetailByUnit'   => [
                'uid' => $uid
            ],
            'appendListImageMapByUnit' => [
                'uid' => $uid
            ],
            'appendGetCatalogInfo'     => [],
            'appendGetVehicleInfo'     => [
                'vid' => $vid
            ]
        ];

        $data = $this->getData($requests, $params);
        if ($data) {
            $unit        = $data[0];
            $details     = $data[1];
            $imagemap    = $data[2]->mapObjects;
            $catalogInfo = $data[3];
            $vehicle     = $data[4];

            $language = new Language();

            $pathway         = new Pathway();
            $fromTask        = $this->input->get('fromTask');
            $fromCatalogTask = $this->input->get('fromTask');

            $pathway->addItem($catalogInfo->name, $catalogInfo->link);

            $pathway->addItem($vehicle->name, !$skipped ? $vehicle->getQGLink(null) :  null);

            $pathway->addItem($unit->name);

            $this->pathway           = $pathway->getPathway();
            $this->vehicle           = $vehicle;
            $this->cataloginfo       = $catalogInfo;
            $this->unit              = $unit;
            $this->imagemap          = $imagemap;
            $this->details           = $details->toGroupsByCodeOnImage();
            $this->catalog           = $this->input->get('c');
            $this->vid               = $this->input->get('vid', '');
            $this->gid               = $this->input->get('gid', '');
            $this->cid               = $this->input->get('cid', '');
            $this->selectedCoi       = $this->input->get('coi', '');
            $this->domain            = $this->getBackUrl();
            $this->cois              = $this->input->get('coi') ? explode(', ',
                $this->input->get('coi')) : '';
            $this->noimage           = Config::$imagePlaceholder;
            $this->fromCatalogTask   = $fromCatalogTask;
            $this->corrected         = $this->input->get('corrected');
            $this->useApplicability  = $catalogInfo->supportdetailapplicability;
            $this->showOems          = Config::$showOemsToGuest;
            $this->showApplicability = Config::$showApplicability;
            $this->linkTarget        = $this->getLinkTarget();

        }

        parent::Display($tpl, $view);
    }

}
