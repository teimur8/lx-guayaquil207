<?php

namespace guayaquil\views\vehicle;

use guayaquil\Config;
use guayaquil\guayaquillib\data\Language;
use guayaquil\guayaquillib\objects\UnitObject;
use guayaquil\modules\pathway\Pathway;
use guayaquil\View;

class VehicleHtml extends View
{

    public function Display($tpl = 'vehicle', $view = 'view')
    {
        $catalogCode   = $this->input->get('c');
        $ssd           = $this->input->get('ssd', '');
        $vid           = $this->input->get('vid');
        $cid           = $this->input->get('cid', -1);
        $linkedWithUnit = $this->input->get('linkedWithUnit');
        $language = new Language();

        $requests = [
            'appendGetCatalogInfo' => [],
            'appendGetVehicleInfo' => [
                'vid' => $vid
            ],
            'appendListCategories' => [
                'vid' => $vid,
                'cid' => $cid
            ],
            'appendListUnits' => [
                'vid' => $vid,
                'cid' => $cid
            ]
        ];

        $params = ['c' => $catalogCode, 'ssd' => $ssd, ''];

        $data = $this->getData($requests, $params);

        if ($data) {
            $catalogInfo = $data[0];
            $vehicle     = $data[1];
            $categories  = $data[2]->root;
            $units       = $data[3]->units;

            if (count($units) === 1 && $linkedWithUnit) {
                $cCategory = null;
                foreach ($categories as $category) {
                    if ($category->categoryid === $cid) {
                        $cCategory = $category;
                    }
                }

                /**
                 * @var UnitObject $unit
                 */
                $unit = reset($units);
                $this->redirect($unit->getLink($vehicle, $cCategory));
            }

            if ($this->input->get('checkQG', false) && $catalogInfo->supportquickgroups) {

                $link = $language->createUrl('qgroups', '', '', [
                    'c'         => $this->input->get('c'),
                    'vid'       => $this->input->get('vid'),
                    'ssd'       => $this->input->get('ssd'),
                    'path_data' => $this->input->get('path_data')
                ]);

                $this->redirect($link);
            }

            $pathway = new Pathway();

            $pathway->addItem($catalogInfo->name, $catalogInfo->link);

            $firstCategory = -1;

            if ($categories) {
                $toShift       = $categories;
                $firstCategory = array_shift($toShift);
            }


            $pathway->addItem($vehicle->brand . ' ' . $vehicle->name);

            $this->pathway = $pathway->getPathway();

            $this->vin               = $this->input->get('vin', '');
            $this->frame             = $this->input->get('frame', '');
            $this->node_id           = $this->input->get('node_id', '');
            $this->cataloginfo       = $catalogInfo;
            $this->vehicle           = $vehicle;
            $this->categories        = $categories;
            $this->units             = $units;
            $this->imageSize         = Config::imageSize;
            $this->cCid              = $this->input->get('cid', '');
            $this->firstCategory     = $firstCategory->categoryid;
            $this->useApplicability  = $catalogInfo ? $catalogInfo->supportdetailapplicability : 0;
            $this->partsList         = isset($data[4]) ? $data[4]->oemParts : null;
            $this->totalParts        = isset($data[4]) ? $this->total = count($data[4]->oemParts) : 0;
            $this->showGrousToGuest  = Config::$showGroupsToGuest;
            $this->showApplicability = Config::$showApplicability;
            $this->linkedWithUnit = $linkedWithUnit;
        }

        parent::Display($tpl, $view);
    }

    function isFeatureSupported($catalogInfo, $featureName)
    {
        $result = false;
        if (isset($catalogInfo->features)) {
            foreach ($catalogInfo->features->feature as $feature) {
                if ((string)$feature['name'] == $featureName) {
                    $result = true;
                    break;
                }
            }
        }

        return $result;
    }

    private function hierarchyCategories($categories, $parent = 0)
    {
        $result = [];

        foreach ($categories as $key => $category) {
            if ($category->parentcategoryid === $parent) {
                $result[$category->categoryid]['attributes'] = $category;

                $hasChildrens = $category->childrens ? true : false;

                if ($hasChildrens) {
                    $result[$category->categoryid]['childrens'] = $this->hierarchyCategories($categories,
                        $category->categoryid);
                }
            }
        }

        return $result;
    }
}




