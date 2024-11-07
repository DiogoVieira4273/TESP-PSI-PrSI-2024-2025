<?php

namespace backend\controllers;

use common\models\Profile;
use common\models\User;
use common\models\UserSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all User models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     * @param int $id
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $profile = Profile::find()->where(['user_id' => $id])->one();
        return $this->render('view', [
            'model' => $this->findModel($id),
            'profile' => $profile,
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */

    public function actionCreate()
    {
        //iniciliaza as variaveis para a criação de um user e de um perfil
        $model = new User();
        $profile = new Profile();

        //guarda os estados disponíveis para um user
        $status = [User::STATUS_ACTIVE => 'Ativo', User::STATUS_INACTIVE => 'Inativo'];

        //se o pedido save por POST
        if ($this->request->isPost) {
            //guarda os campos do form na variavel
            $post = $this->request->post();

            //se tudo for válido
            if ($model->load($post) && $model->validate()) {
                //atribui o respetivo valor para cada campo do utilizador
                $model->username = $post['User']['username'];
                $model->email = $post['User']['email'];
                $model->setPassword($post['User']['password_hash']);
                $model->generateAuthKey();
                $model->status = $post['User']['status'];
                $model->save(false);

                //se o registo do user foi concluído
                if ($model->save()) {

                    //atribui a role funcionário ao user criado
                    $auth = \Yii::$app->authManager;
                    $role = $auth->getRole('funcionario');
                    $auth->assign($role, $model->id);

                    //criar um perfil ao user criado
                    //atribui o respetivo valor para cada campo do perfil
                    $profile->nif = $post['Profile']['nif'];
                    $profile->morada = $post['Profile']['morada'];
                    $profile->telefone = $post['Profile']['telefone'];
                    $profile->user_id = $model->id;

                    //se o registo do perfil foi concluído
                    if ($profile->save()) {
                        //redireciona para a página view do user criado
                        return $this->redirect(['view', 'id' => $model->id]);
                    }
                }
            }
        } else {
            //se ocorrer erro devolve os valores do user inseridos no formulário
            $model->loadDefaultValues();
        }

        //faz render da página de create e manda os dados principais para o user e perfil, e os respetivos status
        return $this->render('create', [
            'model' => $model,
            'profile' => $profile,
            'status' => $status,
        ]);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */

    public function actionUpdate($id)
    {
        //inicializa as variaveis para a edição do user e do perfil
        $model = User::findOne($id);
        $profile = Profile::findOne(['user_id' => $model->id]);

        //guarda os estados disponíveis para um user
        $status = [User::STATUS_ACTIVE => 'Ativo', User::STATUS_INACTIVE => 'Inativo'];

        //se o pedido save por POST
        if ($this->request->isPost) {
            //guarda os campos do form na variavel
            $post = $this->request->post();

            //se tudo for válido
            if ($model->load($post) && $model->validate()) {
                //atribui o respetivo valor para cada campo do utilizador
                $model->username = $post['User']['username'];
                $model->email = $post['User']['email'];

                //se o campo da password não estiver vazio
                if ($post['User']['password_hash'] != null) {
                    $model->setPassword($post['User']['password_hash']);
                }

                //se o user a alterar for o admin principal, não faz este código
                if ($model->id != 1) {
                    $model->status = $post['User']['status'];
                }
                $model->save(false);

                //se a edição do user foi concluída
                if ($model->save()) {

                    //se o user a atualizar os dados for o admin principal, faz redirect para a view, saltando os campos de profile
                    if ($model->id == 1) {
                        return $this->redirect(['view', 'id' => $model->id]);
                    }
                    //editar o perfil do user criado
                    //atribui o respetivo valor para cada campo do perfil
                    $profile->nif = $post['Profile']['nif'];
                    $profile->morada = $post['Profile']['morada'];
                    $profile->telefone = $post['Profile']['telefone'];

                    //se a edição do perfil foi concluída
                    if ($profile->save()) {
                        //redireciona para a página view do user editado
                        return $this->redirect(['view', 'id' => $model->id]);
                    }
                }
            }
        } else {
            //se ocorrer erro devolve os valores do user inseridos no formulário
            $model->loadDefaultValues();
        }

        //faz render da página de create e manda os dados principais para o user e perfil, e os respetivos status
        return $this->render('update', [
            'model' => $model,
            'profile' => $profile,
            'status' => $status,
        ]);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        if ($id != 1) {
            $profile = Profile::findOne(['user_id' => $id]);
            $profile->delete();
            $this->findModel($id)->delete();
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
