<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use View;
use Redirect;
use App\Payment_Model as Payment_Model;
use App\User;


class PaymentController extends Controller
{

    public function index() {
    }

    public function createCustomer() {

        // create customer 
        // \Stripe\Stripe::setApiKey("sk_test_JdYnD1KMVAJVb91I0mEPEXoE"); // Lawrence's one
      // \Stripe\Stripe::setApiKey("sk_test_hXdEuBgLNpy5C4TTzUAhQKOS"); // Alvin's one
        \Stripe\Stripe::setApiKey("sk_test_nFj86wyB4uAtLOWIGeDJc6Cy");  //  CHENGCHEUKHENG@GMAIL.COM
        $customer =  \Stripe\Customer::create(array(
        // 'email' => $_POST['stripeEmail'],
        'email' => 'testing@gmail.com',
        "description" => "Customer for testing",
        // "source" => "tok_amex" // obtained with Stripe.js
        "source" => array(
            "exp_month" => 10,
            "exp_year" => 19,
            "number" => "378282246310005",
            "object" => "card",
            ),
        ));
        // echo hi;
      echo '<pre>' . print_r($customer, 1) . '</pre>';
      // echo $customer['id'];
    }

    public function createCard() {
         //create Card
        \Stripe\Stripe::setApiKey("sk_test_hXdEuBgLNpy5C4TTzUAhQKOS");

        // $customer = \Stripe\Customer::retrieve("cus_B5DTVVjfC9HWqZ");
        $customer = \Stripe\Customer::retrieve("cus_B5xLWyIXNuAOQp");
        $customer->sources->create(array("source" => "tok_mastercard"));
        // echo "hi";
        echo '<pre>' . print_r($customer, 1) . '</pre>';
    }

    public function createCharge() {
        // creeate charge

       \Stripe\Stripe::setApiKey("sk_test_nFj86wyB4uAtLOWIGeDJc6Cy"); // Henneth's one

        $charge = \Stripe\Charge::create(array(
          "amount" => 2000,
          "currency" => "HKD",
          // "source" => "tok_visa", // obtained with Stripe.js
          "source" => array(
            "exp_month" => 10,
            "exp_year" => 19,
            "number" => "378282246310005",
            "object" => "card",
            ),
          "description" => "Charge for aava.williams@example.com"
        ));
        echo '<pre>' . print_r($charge, 1) . '</pre>';
    }

    public function retrieveCharge() {
        // retrieve a chrage

        \Stripe\Stripe::setApiKey("sk_test_hXdEuBgLNpy5C4TTzUAhQKOS");

        $receiper = \Stripe\Charge::retrieve("cus_B5xLWyIXNuAOQp");
        echo "hi";
        echo '<pre>' . print_r($receiper, 1) . '</pre>';
    }
}
