<?php

namespace YahooJapan\YConnect\Tests;

use \YConnectClient;
use \OAuth2ResponseType;
use \OIDConnectScope;
use \OIDConnectDisplay;
use \OIDConnectPrompt;
use \ClientCredential;

class YConnectClientTest extends \PHPUnit_Framework_TestCase
{
    private $clientCredential;
    private $responseType;

    public function setUp()
    {
        // アプリケーションID, シークレット
        $clientId     = "YOUR_APPLICATION_ID";
        $clientSecret = "YOUR_SECRET";

        // 各パラメータ初期化
        $redirectUri = "http://localhost/";

        // リクエストとコールバック間の検証用のランダムな文字列を指定してください
        $this->state = "44Oq44Ki5YWF44Gr5L+644Gv44Gq44KL77yB";
        // リプレイアタック対策のランダムな文字列を指定してください
        $this->nonce = "5YOV44Go5aWR57SE44GX44GmSUTljqjjgavjgarjgaPjgabjgog=";

        $responseType = OAuth2ResponseType::CODE_IDTOKEN;
        $scope = array(
            OIDConnectScope::OPENID,
            OIDConnectScope::PROFILE,
            OIDConnectScope::EMAIL,
            OIDConnectScope::ADDRESS
        );
        $display = OIDConnectDisplay::DEFAULT_DISPLAY;
        $prompt = array(
            OIDConnectPrompt::DEFAULT_PROMPT
        );

        // クレデンシャルインスタンス生成
        $this->clientCredential = new ClientCredential($clientId, $clientSecret);
    }

    public function testStateYConnectClient()
    {
        $client = $this->getMock('YConnectClient', array('getAuthorizationCode'), array($this->clientCredential));
        $client
            ->expects($this->any())
            ->method('getAuthorizationCode')
            ->will($this->returnValue('abc'))
        ;
        $this->assertEquals($client->getAuthorizationCode($this->state), 'abc');
    }
}
