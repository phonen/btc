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

    public function h48()
    {

        $param = $this->request->param();
        if(isset($param['order'])){
            if(isset($param['sort']))
            $order = isset($param['order'])?$param['order']:'desc';
            else $order = 'desc';
            $sort = isset($param['sort'])?$param['sort']:'volume12';
            $offset = isset($param['offset'])?$param['offset']:0;
            $limit = isset($param['limit'])?$param['limit']:10;
            $total = Db::connect('db_coinmarket')->name("coinmarket_48h")->count();
            $prices = Db::connect('db_coinmarket')->name("coinmarket_last")->field("price_usd as price")->where("coinid='bitcoin'")->find();

            /*
            foreach ($coinmarket as $coin){
                if($coin['id'] == "bitcoin") {
                    $bitcoin = $coin['price'];
                    break;
                }
            }
            */
            if($prices){
                $bitcoin = $prices['price'];
                $coinmarket = Db::connect('db_coinmarket')->name("coinmarket_48h")->field("id,symbol , price,volume,price-price12 as price12,(price-price12)/price as price12p,(price-price24)/price as price24p,volume-volume12 as volume12,price-price48 as price48,volume-volume48 as volume48,price-price24 as price24,volume-volume24 as volume24")->order($sort . " " . $order)->limit($offset,$limit)->select();
                $coins = array();
                foreach($coinmarket as $coin){
                    $coin['price12'] = round($coin['price12'],4);
                    $coin['price12p'] = round($coin['price12p']*100,2) . "%";
                    $coin['price24p'] = round($coin['price24p']*100,2) . "%";


                    $coin['volume'] = round($coin['volume']/$bitcoin,2);
                    $coin['volume12'] = round($coin['volume12']/$bitcoin,2);
                    $coin['volume24'] = round($coin['volume24']/$bitcoin,2);
                    $coin['volume48'] = round($coin['volume48']/$bitcoin,2);

                    array_push($coins,$coin);
                }

            }
            /*
            $coins = array();
            foreach($coinmarket as $coin){
                $coin['price12'] = round($coin['price12'],4);
                if($coin['price'] != 0){
                    $coin['price12p'] = round($coin['price12'] /$coin['price']*100,2) . "%";
                    $coin['price24p'] = round($coin['price24'] /$coin['price']*100,2) . "%";
                }

                $coin['volume'] = round($coin['volume']/$bitcoin,2);
                $coin['volume12'] = round($coin['volume12']/$bitcoin,2);
                $coin['volume24'] = round($coin['volume24']/$bitcoin,2);
                $coin['volume48'] = round($coin['volume48']/$bitcoin,2);

                array_push($coins,$coin);
            }
            */
            echo json_encode(array("total"=>$total,"rows"=>$coins));
        }
        else return $this->fetch(':h48');

    }
    public function h12()
    {
        header("location:https://btc.exeou.com/coinmarket/Index/h48");
        exit();
        $param = $this->request->param();
        if(isset($param['order'])){
            $coinmarket = Db::connect('db_coinmarket')->name("coinmarket_48h")->field("id,symbol , price,volume,price-price12 as price12,volume-volume12 as volume12,price-price48 as price48,volume-volume48 as volume48")->order("volume12 desc")->select();
            foreach ($coinmarket as $coin){
                if($coin['id'] == "bitcoin") {
                    $bitcoin = $coin['price'];
                    break;
                }
            }
            $coins = array();
            foreach($coinmarket as $coin){
                $coin['price12'] = round($coin['price12'],4);
                if($coin['price'] != 0)
                    $coin['price12p'] = round($coin['price12'] /$coin['price']*100,2) . "%";
                $coin['volume12'] = round($coin['volume12']/$bitcoin,2);
                $coin['volume48'] = round($coin['volume48']/$bitcoin,2);

                array_push($coins,$coin);
            }

            echo json_encode($coins);
        }
        else return $this->fetch(':h12');
    }
}
