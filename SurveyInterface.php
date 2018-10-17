<?php
/**
 * Created by PhpStorm.
 * User: kozhevnikov
 * Date: 23/10/2017
 * Time: 15:43
 */

namespace onmotion\survey;


interface SurveyInterface
{
    /**
     * Returns user models founded by token
     *
     * @param $token string
     * @return User
     */
    static function actionGetRespondentsByToken($token);

    /**
     * Returns username for render
     *
     * @return string
     */
    public function getFullname();

}