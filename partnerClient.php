<?php

/**
 * Copyright 2017 @ Caribation SRL
 * Author: Richard Urban
 * Date: 27.08.2017
 *
 * A simple class to check availability of Taxi Shares for a specific flight and date combination.
 * You must obtain the credentials (protected vars) from Taxi Share to make it work
 */
class partnerClient
{

    protected $user                 = '';   // assigned by Taxi Share - please request
    protected $password             = '';   // assigned by Taxi Share - please request

    protected $fullResult           = false;
    protected $curlDestinationRoot  = 'http://taxisharers.com/query/share/';


    /**
     * partnerClient constructor.
     *
     * @param bool $fullResult In the future the client will be able to receive response as an array
     * with some details about the taxi share offer, such as rider's message.
     */
    function __construct( $fullResult = false) {

        if ( version_compare( phpversion(), '5.4.0') < 0) {
            die('PHP version >= 5.4.0 required. You are using ' . phpversion());
        }

        if ( empty( $this->user) || empty( $this->password)) {
            die('Please obtain credentials from agent.support@taxisharers.com');
        }

        $this->fullResult = $fullResult;

    }


    /**
     * @param $mmddDate
     * @param $flightNumber
     *
     * The function is a switch to determine the type of result expected
     *
     * @return array|null
     */
    function getTaxiShares($mmddDate, $flightNumber) {

        if( $this->fullResult) {
            // not implemented in this version
            return $this->obtainShareDetails( $mmddDate, $flightNumber);
        } else {
            return $this->checkTaxiShares( $mmddDate, $flightNumber);
        }
    }


    /**
     * @param string $mmddDate. Must be in the format mmdd. Example: 1201 for the 1st of December
     * @param string $flightNumber. Must start 1 to 3 with alpha chars followed by digits. Example: XX9999
     * For the test purposes the system will return 200 => 'SUCCESS' for a fictitious flight XX9999 on 1231
     *
     * @return int
     * The return value is an integer corresponding to http_response_code():
     *
     * 200 => 'SUCCESS' Shares exist
     * 400 => 'No shares found!'
     * 417 => 'Invalid request!' such as bad params
     * 500 => 'Unknown error'
     *
     */
    function checkTaxiShares($mmddDate, $flightNumber) {

        if ( ! $this->validParams($mmddDate, $flightNumber)) {
            return 400;
        }

        $initUrl  = 'https://taxisharers.com/query/share/';
        $initUrl .= $mmddDate . '/' . $flightNumber;
        $ch = curl_init( $initUrl);
        if ( $ch === false) {
            return 500;
        }

        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC ) ;
        curl_setopt($ch, CURLOPT_USERPWD, $this->user . ':' . $this->password);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $result     = curl_exec($ch);
        $curlError  = curl_error($ch);
        unset( $ch);

        $httpResponse = empty( $curlError) ? http_response_code() : '500';

        if ( in_array( $httpResponse, array( '200', '417', '400'))) {
            return $httpResponse;
        } else {
            return 500;
        }
    }


    /**
     * @param string $mmddDate. Must be in the format mmdd. Example: 1201 for the 1st of December
     * @param string $flightNumber. Must start 1 to 3 with alpha chars followed by digits. Example: XX9999
     *
     * This function will be implemented in the next version
     *
     * If taxi shares are found an array with details such as message in the offering
     * will be returned
     *
     * @return null
     */
    function obtainShareDetails( $mmddDate, $flightNumber) {

        return null;
    }


    /**
     * @param $mmddDate
     * @param $flightNumber
     * @return bool
     */
    function validParams($mmddDate, $flightNumber) {

        if (! $this->validFlightNumber( $flightNumber) ) {
            return false;
        }
        if (! $this->validmmddDate( $mmddDate) ) {
            return false;
        }

        return true;
    }


    /**
     * @param string $flight Must start with 1 - 3 alphas followed by digits
     * The server will ignore starting zeros in digits. They cause no problem
     * @return bool
     */
    function validFlightNumber($flight) {

        $firstLetter    = substr( $flight, 0, 1);
        $secondLetter   = substr( $flight, 1, 1);
        $thirdLetter    = substr( $flight, 2, 1);

        // first letter mut be alpha
        if ( ! ctype_alpha( $firstLetter)) {
            return false;
        }

        // starts with 1 letter
        if ( ! ctype_alpha( $secondLetter)  ) {
            $restDigits = substr( $flight, 1);
        }
        // starts with 2 letters
        if ( ctype_alpha( $secondLetter) && ! ctype_alpha( $thirdLetter)  ) {
            $restDigits = substr( $flight, 2);
        }
        // starts with 3 letters
        if ( ctype_alpha( $secondLetter) && ctype_alpha( $thirdLetter)  ) {
            $restDigits = substr( $flight, 3);
        }

        if ( ! is_numeric( $restDigits)) {
            return false;
        }

        return true;
    }


    /**
     * @param string $mmdd
     * Four digits. mm represents a month (01-12) and dd a day (01 - 31)
     * Example: 0125 for January 25th
     * Must have leading zero for days or month < 10
     *
     * @return bool
     */
    function validmmddDate($mmdd) {

        if( is_numeric( $mmdd) && strlen( $mmdd) == 4) {
            $mm = (int) substr( $mmdd, 0, 2);
            $dd = (int) substr( $mmdd, 2, 2);
            if( $mm > 0 && $mm < 13 && $dd > 0 && $dd < 32) {
                return true;
            }
        }
        return false;
    }

}