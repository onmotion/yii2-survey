<?php

namespace common\modules\survey\widgetControllers;

use common\modules\survey\models\search\SurveySearch;
use common\modules\survey\models\search\SurveyStatSearch;
use common\modules\survey\models\Survey;
use common\modules\survey\models\SurveyAnswer;
use common\modules\survey\models\SurveyQuestion;
use common\modules\survey\models\SurveyStat;
use common\modules\survey\SurveyInterface;
use common\modules\survey\User;
use yii\base\Model;
use yii\base\UserException;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Default controller for the `survey` module
 */
class DefaultController extends Controller
{



    protected function findModel($id)
    {
        if (($model = Survey::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


}
