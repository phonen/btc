<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 老猫 <thinkcmf@126.com>
// +----------------------------------------------------------------------
namespace app\coinmarket\controller;

use cmf\controller\HomeBaseController;
use think\Db;

class IndexController extends HomeBaseController
{
    public function index()
    {
        $coinmarket = Db::connect('db_coinmarket')->name("coinmarket_last")->field("symbol,price_usd,volume")->order("volume desc")->select();
        foreach ($coinmarket as $coin){
            if($coin['symbol'] == 'BTC') {
                $price = $coin['price_usd'];
                break;
            }
        }
        $this->assign("price",$price);

        $this->assign("coinmarket",$coinmarket);

        return $this->fetch(':index');
    }

    public function h12()
    {
        $coinmarket = Db::connect('db_coinmarket')->name("coinmarket_48h")->field("id,symbol , price,volume,price-price12 as price12,volume-volume12 as volume12,price-price48 as price48,volume-volume48 as volume48")->order("volume12 desc")->select();

        foreach($coinmarket as $coin){
            $price[$coin['id']] = $coin['price'];
        }
        $this->assign("price",$price);
        $this->assign("coinmarket",$coinmarket);
        return $this->fetch(':h12');
    }
}
