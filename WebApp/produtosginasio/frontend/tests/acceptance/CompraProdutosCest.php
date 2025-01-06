<?php


namespace frontend\tests\acceptance;

use frontend\tests\AcceptanceTester;

class CompraProdutosCest
{
    public function _before(AcceptanceTester $I)
    {
        /*$I->amOnPage('/login');
        $I->fillField('LoginForm[username]', 'Tuga Francisco');
        $I->fillField('LoginForm[password]', 'Admin*1234567');
        $I->click('Login');*/
    }

    // tests
    public function testComprarProdutos(AcceptanceTester $I)
    {
        $I->amOnPage('/login');
        $I->fillField('LoginForm[username]', 'Tuga Francisco');
        $I->fillField('LoginForm[password]', 'Admin*1234567');
        $I->click('Login');
        //
        $I->see('Produtos');
        $I->click('Produtos');
        $I->wait(2);
        $I->see('Polo Adidas');
        $I->click('Polo Adidas');
    }
}
