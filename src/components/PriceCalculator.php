<?php

namespace Components;

class PriceCalculator 
{
    private $products = [];
    
    public function __construct($checkout)
    {
        if (sizeof($checkout) > 0) 
        {
            foreach ($checkout as $item) 
            {
                $product = \Models\Product::fetchOne([
                    'isDeleted' => [
                        '$ne' => true
                    ],
                    'type' => 'final',
                    'id' => $item['id']
                ]);
                
                if (!$product) {
                    throw new \Exception('Can not process order');
                }
                
                $this->products[] = [
                    'product' => $product, 
                    'count' => $item['count']
                ];
            }
        }
    }
    
    private function calcPrice($realPrice, $discountType, $discountValue) 
    {
        switch ($discountType) 
        {
            case 'const':
                return $realPrice - $discountValue;
            break;

            case '%':
                return $realPrice - $realPrice * $discountValue * 0.01;
            break;

            default:
                return $realPrice;
            break;
        }
    }
    
    public function getFinalPrice() 
    {
        $price = 0;
        
        foreach ($this->products as $checkout) 
        {
            // If product discount period is over
            if (!empty($checkout['product']->discountTimeout) && $checkout['product']->discountTimeout * 0.001 < time()) 
            {
                $checkout['product']->discount = '';
            }
            
            $price += $this->calcPrice(
                $checkout['product']->price, 
                $checkout['product']->discountType, 
                $checkout['product']->discount
            ) * $checkout['count'];
        }
        
        return $price;
    }
}
