<?php
/**
 * Created by Laximo.
 * User: elnikov.a
 * Date: 17.08.17
 * Time: 16:03
 */

namespace guayaquil\views\detailfilter;

use guayaquil\Config;
use guayaquil\guayaquillib\objects\FilterObject;
use guayaquil\guayaquillib\objects\UnitObject;
use guayaquil\View;

/**
 * @property FilterObject filters
 * @property string       domain
 * @property UnitObject   detail
 * @property string       oem
 * @property string       brand
 * @property array        from
 * @property string       fromTask
 * @property string       fromCatalogTask
 * @property string       linkTarget
 */
class DetailfilterHtml extends View
{
    public function Display($tpl = 'detailfilter', $view = 'view')
    {
        $catalogCode = $this->input->get('c');
        $ssd         = $this->input->get('ssd', '');
        $f           = $this->input->get('f');
        $vid         = $this->input->get('vid');
        $uid         = $this->input->get('uid');
        $did         = $this->input->get('did');
        $params      = ['c' => $catalogCode, 'ssd' => $ssd, ''];

        $requests = [
            'appendGetFilterByDetail' => [
                'f'   => $f,
                'vid' => $vid,
                'uid' => $uid,
                'did' => $did
            ],
            'appendGetUnitInfo'       => [
                'uid' => $uid
            ]
        ];

        $data = $this->getData($requests, $params);

        if ($data) {
            $fromTask        = $this->input->get('fromTask');
            $fromCatalogTask = $this->input->get('fromCatalogTask');
            $filters         = $data[0];
            $detail          = $data[1];

            $this->filters         = $filters;
            $this->domain          = Config::$useEnvParams ? $this->getBackUrl() : 'javascript:void(0)';
            $this->detail          = $detail;
            $this->linkTarget      = $this->getLinkTarget();
            $this->oem             = $this->input->get('oem');
            $this->brand           = $this->input->get('brand');
            $this->from            = $this->input->getArray();
            $this->fromTask        = $fromTask;
            $this->fromCatalogTask = $fromCatalogTask;
        }

        parent::Display($tpl, $view);
    }

}
