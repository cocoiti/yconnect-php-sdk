<?php
/**
 * The MIT License (MIT)
 * 
 * Copyright (C) 2013 Yahoo Japan Corporation. All Rights Reserved. 
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/** \file IdTokenUtil.inc
 *
 */

/**
 * \class IdToken Verifation Utilクラス
 *
 */
class IdTokenUtil
{
    private $object = NULL;

    private $issuer = "https://auth.login.yahoo.co.jp";

    private $auth_nonce = NULL;

    private $client_id = NULL;

    private $acceptable_range = 600; // sec

    /**
     * Constructor
     */
    public function __construct( $object, $auth_nonce, $client_id )
    {
        $this->object     = $object;
        $this->auth_nonce = $auth_nonce;
        $this->client_id  = $client_id;
    }

    public function verify()
    {
        // Is iss equal to issuer ?
        if ( $this->issuer != $this->object->iss )
            throw new IdTokenException( "Invalid issuer.", "The issuer did not match.({$this->object->iss})" );

        // Is nonce equal to this nonce (was issued at the request authorization) ?
        if ( $this->auth_nonce != $this->object->nonce )
            throw new IdTokenException( "Not match nonce.", "The nonce did not match.({$this->auth_nonce}, {$this->object->nonce})" );

        // Is aud equal to the client_id (Application ID) ?  if ( $this->client_id != $this->object->aud )
        if ( $this->client_id != $this->object->aud )
            throw new IdTokenException( "Invalid audience.", "The client id did not match.({$this->object->aud})" );

        // Is corrent time less than exp ?
        if ( time() > $this->object->exp )
            throw new IdTokenException( "Expired ID Token.", "Re-issue Id Token.({$this->object->exp})" );

        YConnectLogger::debug( "current time: " . time() . ", exp: {$this->object->exp}(" . get_class() . "::" . __FUNCTION__ . ")" );

        // prevent attacks
        $time_diff = time() - $this->object->iat;
        if ( $time_diff > $this->acceptable_range )
            throw new IdTokenException( "Over acceptable range.", "This access has expired possible.({$time_diff} sec)" );

        YConnectLogger::debug( "current time - iat = {$time_diff}, current time: " . time() .
            ", iat: {$this->object->iat}(" . get_class() . "::" . __FUNCTION__ . ")" );

        return true;
    }

    public function setIssuer( $issuer )
    {
        $this->issuer = $issuer;
    }

    public function setAcceptableRange( $acceptable_range )
    {
        $this->acceptable_range = $acceptable_range;
    }
}

/* vim:ts=4:sw=4:sts=0:tw=0:ft=php:set et: */
