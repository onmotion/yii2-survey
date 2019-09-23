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
     * Example:
     *
     * static function actionGetRespondentsByToken($token)
     * {
     *
     * $query = UserSearch::find()->joinWith('profile');
     *
     * $fullName = str_replace(' ', '% %', htmlentities($token));
     * $query->andWhere("concat(profile.last_name, ' ', profile.first_name, ' ', profile.second_name) LIKE '%" . $fullName . "%'");
     *
     * $userList = $query->select(['id', "concat(profile.last_name, ' ', profile.first_name, ' ', profile.second_name, ' (', id, ')') as fullname"])
     * ->all();
     *
     * return $userList;
     * }
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
