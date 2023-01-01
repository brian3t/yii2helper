<?php

namespace usv\yii2helper;
/**
 * User: tri
 * Date: 12/31/22
 */
class YiiHelper
{
  /**
   * Request is ajax
   * @param \yii\web\Request|bool $req
   */
  public static final function req_is_ajax(\yii\web\Request|bool $req = false) {
    if (! $req){
      $req = \Yii::$app->request;
    }
    if ($req->post('s_is_ajax') && ($req->post('s_is_ajax') == 'false' )) return false;
    return $req->isAjax;
  }
}
