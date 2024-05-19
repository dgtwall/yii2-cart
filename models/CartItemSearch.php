<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\CartItem;

/**
 * CartItemSearch represents the model behind the search form of `app\models\CartItem`.
 */
class CartItemSearch extends CartItem
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'shopId', 'productId', 'addUserId', 'skuId', 'productNum', 'status'], 'integer'],
            [['addPrice', 'discountPrice', 'payPrice'], 'number'],
            [['addTime', 'createdTime', 'updatedTime', 'deletedTime', 'cartId'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = CartItem::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'shopId' => $this->shopId,
            'cartId' => $this->cartId,
            'productId' => $this->productId,
            'addUserId' => $this->addUserId,
            'skuId' => $this->skuId,
            'productNum' => $this->productNum,
            'status' => $this->status,
            'addPrice' => $this->addPrice,
            'discountPrice' => $this->discountPrice,
            'payPrice' => $this->payPrice,
            'addTime' => $this->addTime,
            'createdTime' => $this->createdTime,
            'updatedTime' => $this->updatedTime,
            'deletedTime' => $this->deletedTime,
        ]);

        $query->andFilterWhere(['=', 'cartId', $this->cartId]);

        return $dataProvider;
    }
}
