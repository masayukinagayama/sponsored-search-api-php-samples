<?php
/**
 * Sample Program for AdCustomizerSample.
 * Copyright (C) 2012 Yahoo Japan Corporation. All Rights Reserved.
 */
require_once(dirname(__FILE__) . '/../../conf/api_config.php');
require_once(dirname(__FILE__) . '/../util/SoapUtils.class.php');
require_once(dirname(__FILE__) . '/../adSample/CampaignServiceSample.php');
require_once(dirname(__FILE__) . '/../adSample/AdGroupServiceSample.php');
require_once(dirname(__FILE__) . '/../adSample/AdGroupCriterionServiceSample.php');
require_once(dirname(__FILE__) . '/../adSample/AdGroupAdServiceSample.php');
require_once(dirname(__FILE__) . '/FeedFolderServiceSample.php');
require_once(dirname(__FILE__) . '/FeedItemServiceSample.php');

/**
 * CampaignService::mutate(ADD)
 *
 * @param string $accountId Account ID
 * @return array CampaignValues entity
 * @throws Exception
 */
function createCampaign($accountId){
    // Set Operand
    $operand = array(
        // Set ManualCpc Campaign
        array(
            'accountId' => $accountId,
            'campaignName' => 'SampleCampaign_CreateOn_' . SoapUtils::getCurrentTimestamp(),
            'userStatus' => 'ACTIVE',
            'startDate' => '20300101',
            'endDate' => '20301231',
            'budget' => array(
                'period' => 'DAILY',
                'amount' => 1000,
                'deliveryMethod' => 'STANDARD',
            ),
            'biddingStrategyConfiguration' => array(
                'biddingStrategyType' => 'MANUAL_CPC',
            ),
            'adServingOptimizationStatus' => 'ROTATE_INDEFINITELY',
            'settings' => array(
                    array(
                        'type' => 'GEO_TARGET_TYPE_SETTING',
                        'positiveGeoTargetType' => 'AREA_OF_INTENT',
                    ),
            ),
        ),
    );

    //xsi:type for settings
    $operand[0]['settings'][0] =
        new SoapVar($operand[0]['settings'][0],
            SOAP_ENC_OBJECT, 'GeoTargetTypeSetting', API_NS, 'settings', XMLSCHEMANS);

    // Set Request
    $campaignRequest = array(
        'operations' => array(
            'operator' => 'ADD',
            'accountId' => $accountId,
            'operand' => $operand,
        ),
    );

    // Call API
    $campaignService = SoapUtils::getService('CampaignService');
    $campaignResponse = $campaignService->invoke('mutate', $campaignRequest);

    // Response
    if (isset($campaignResponse->rval->values)) {
        if (is_array($campaignResponse->rval->values)) {
            $campaignReturnValues = $campaignResponse->rval->values;
        } else {
            $campaignReturnValues = array($campaignResponse->rval->values);
        }
    } else {
        throw new Exception("No response of add CampaignService.");
    }

    // Error
    foreach ($campaignReturnValues as $campaignReturnValue) {
        if (!isset($campaignReturnValue->campaign)) {
            throw new Exception("Fail to add CampaignService.");
        }
    }

    return $campaignReturnValues;
}

/**
 * AdGroupService::mutate(ADD)
 *
 * @param string $accountId Account ID
 * @param string $campaignId Campaign ID
 * @return array AdGroupValues entity
 * @throws Exception
 */
function createAdGroup($accountId, $campaignId){
    // Set Operand
    $operand = array(
        // Set ManualCpc AdGroup
        array(
            'accountId' => $accountId,
            'campaignId' => $campaignId,
            'adGroupName' => 'SampleAdGroup_CreateOn_' . SoapUtils::getCurrentTimestamp(),
            'userStatus' => 'ACTIVE',
            'biddingStrategyConfiguration' => array(
                'biddingStrategyType' => 'MANUAL_CPC',
                'initialBid' => array(
                    'maxCpc' => 120,
                ),
            ),
        ),
    );

    // Set Request
    $adGroupRequest = array(
        'operations' => array(
            'operator' => 'ADD',
            'accountId' => $accountId,
            'campaignId' => $campaignId,
            'operand' => $operand,
        ),
    );

    // Call API
    $adGroupService = SoapUtils::getService('AdGroupService');
    $adGroupResponse = $adGroupService->invoke('mutate', $adGroupRequest);

    // Response
    if (isset($adGroupResponse->rval->values)) {
        if (is_array($adGroupResponse->rval->values)) {
            $adGroupReturnValues = $adGroupResponse->rval->values;
        } else {
            $adGroupReturnValues = array($adGroupResponse->rval->values);
        }
    } else {
        throw new Exception("No response of add AdGroupService.");
    }

    // Error
    foreach ($adGroupReturnValues as $adGroupReturnValue) {
        if (!isset($adGroupReturnValue->adGroup)) {
            throw new Exception("Fail to add AdGroupService.");
        }
    }

    return $adGroupReturnValues;
}

/**
 * AdGroupCriterionService::mutate(ADD)
 *
 * @param string $accountId Account ID
 * @param string $campaignId Campaign ID
 * @param string $adGroupId Ad group ID
 * @return array AdGroupCriterionValues entity
 * @throws Exception
 */
function createAdGroupCriterion($accountId, $campaignId, $adGroupId){
    // Set Operand
    $operand = array(
        array(
            'accountId' => $accountId,
            'campaignId' => $campaignId,
            'adGroupId' => $adGroupId,
            'criterionUse' => 'BIDDABLE',
            'criterion' => array(
                'type' => 'KEYWORD',
                'text' => 'sample Value',
                'matchType' => 'EXACT'
            ),
            'userStatus' => 'ACTIVE',
            'destinationUrl' => 'http://www.yahoo.co.jp/',
            'biddingStrategyConfiguration' => array(
                'bid' => array(
                    'maxCpc' => 100,
                ),
            ),
        ),
    );

    //xsi:type for criterion Keyword
    $operand[0]['criterion'] =
    new SoapVar($operand[0]['criterion'], SOAP_ENC_OBJECT, 'Keyword', API_NS, 'criterion', XMLSCHEMANS);
    //xsi:type for operand BiddableAdGroupCriterion
    $operand[0] =
    new SoapVar($operand[0], SOAP_ENC_OBJECT, 'BiddableAdGroupCriterion', API_NS, 'operand', XMLSCHEMANS);

    // Set Request
    $adGroupCriterionRequest = array(
        'operations' => array(
            'operator' => 'ADD',
            'accountId' => $accountId,
            'campaignId' => $campaignId,
            'adGroupId' => $adGroupId,
            'criterionUse' => 'BIDDABLE',
            'operand' => $operand,
        ),
    );

    // Call API
    $adGroupCriterionService = SoapUtils::getService('AdGroupCriterionService');
    $adGroupCriterionResponse = $adGroupCriterionService->invoke('mutate', $adGroupCriterionRequest);

    // Response
    if (isset($adGroupCriterionResponse->rval->values)) {
        if (is_array($adGroupCriterionResponse->rval->values)) {
            $adGroupCriterionReturnValues = $adGroupCriterionResponse->rval->values;
        } else {
            $adGroupCriterionReturnValues = array($adGroupCriterionResponse->rval->values);
        }
    } else {
        throw new Exception("No response of add AdGroupCriterionService.");
    }

    // Error
    foreach ($adGroupCriterionReturnValues as $adGroupCriterionReturnValue) {
        if (!isset($adGroupCriterionReturnValue->adGroupCriterion)) {
            throw new Exception("Fail to add AdGroupCriterionService.");
        }
    }

    return $adGroupCriterionReturnValues;
}

/**
 * AdGroupAdService(ADD)
 *
 * @param string $accountId Account ID
 * @param string $campaignId Campaign ID
 * @param string $adGroupId Ad group ID
 * @param string $feedFolderName Feed Folder Name
 * @param array $feedAttributeNames Feed Attribute Names
 * @return array AdGroupAdValues entity
 * @throws Exception
 */
function createAdGroupAd($accountId, $campaignId, $adGroupId, $feedFolderName, $feedAttributeNames){
    // Set Operand
    $operand = array(
        // Set TextAd2(Keyword)
        array(
            'accountId' => $accountId,
            'campaignId' => $campaignId,
            'adGroupId' => $adGroupId,
            'adName' => 'SampleTextAd2_CreateOn_' . SoapUtils::getCurrentTimestamp(),
            'ad' => array(
                'type' => 'TEXT_AD2',
                'headline' => 'sample headline',
                'description' => 'sample {KEYWORD:keyword}',
                'description2' => 'sample {KEYWORD:keyword}',
                'url' => 'http://www.yahoo.co.jp/',
                'displayUrl' => 'www.yahoo.co.jp',
                'devicePreference' => 'SMART_PHONE',
            ),
            'userStatus' => 'ACTIVE',
        ),
        // Set TextAd2(CountdownOption)
        array(
            'accountId' => $accountId,
            'campaignId' => $campaignId,
            'adGroupId' => $adGroupId,
            'adName' => 'SampleCountdownOptionAd_' . SoapUtils::getCurrentTimestamp(),
            'ad' => array(
                'type' => 'TEXT_AD2',
                'headline' => 'sample headline',
                'description' => '{=COUNTDOWN("2016/12/15 18:00:00","ja")}',
                'description2' => 'sample ad desc',
                'url' => 'http://www.yahoo.co.jp/',
                'displayUrl' => 'www.yahoo.co.jp',
                'devicePreference' => 'SMART_PHONE',
            ),
            'userStatus' => 'ACTIVE',
        ),
        // Set TextAd2(CountdownOption&AD_CUSTOMIZER_DATE)
        array(
            'accountId' => $accountId,
            'campaignId' => $campaignId,
            'adGroupId' => $adGroupId,
            'adName' => 'SampleCountdownOfAdCustomizer_' . SoapUtils::getCurrentTimestamp(),
            'ad' => array(
                'type' => 'TEXT_AD2',
                'headline' => 'sample headline',
                'description' => '{=COUNTDOWN('.$feedFolderName.'.'.$feedAttributeNames['AD_CUSTOMIZER_DATE'].',"ja")}',
                'description2' => 'sample ad desc',
                'url' => 'http://www.yahoo.co.jp/',
                'displayUrl' => 'www.yahoo.co.jp',
                'devicePreference' => 'SMART_PHONE',
            ),
            'userStatus' => 'ACTIVE',
        ),
    );

    //xsi:typ for ad of TextAd2
    foreach ($operand as $adGroupAdKey => $adGroupAdValue){
        $operand[$adGroupAdKey]['ad'] = new SoapVar($operand[$adGroupAdKey]['ad'],SOAP_ENC_OBJECT, 'TextAd2',API_NS,'ad',XMLSCHEMANS);
    }

    // Set Request
    $adGroupAdRequest = array(
        'operations' => array(
            'operator' => 'ADD',
            'accountId' => $accountId,
            'operand' => $operand,
        ),
    );

    // Call API
    $adGroupAdService = SoapUtils::getService('AdGroupAdService');
    $adGroupAdResponse = $adGroupAdService->invoke('mutate', $adGroupAdRequest);

    // Response
    if (isset($adGroupAdResponse->rval->values)) {
        if (is_array($adGroupAdResponse->rval->values)) {
            $adGroupAdReturnValues = $adGroupAdResponse->rval->values;
        } else {
            $adGroupAdReturnValues = array($adGroupAdResponse->rval->values);
        }
    } else {
        throw new Exception("No response of add AdGroupAdService.");
    }

    // Error
    foreach ($adGroupAdReturnValues as $adGroupAdReturnValue) {
        if (!isset($adGroupAdReturnValue->adGroupAd)) {
            throw new Exception("Fail to add AdGroupAdService.");
        }
    }

    return $adGroupAdReturnValues;
}


if (__FILE__ != realpath($_SERVER['PHP_SELF'])) {
    return;
}

try{
    $feedFolderService = new FeedFolderServiceSample();
    $feedItemService = new FeedItemServiceSample();

    $accountId = SoapUtils::getAccountId();
    $campaignId = 0;
    $adGroupId = 0;
    $feedFolderId = 0;
    $feedFolderName = null;
    $feedAttributeNames = array(
        'AD_CUSTOMIZER_INTEGER' => null,
        'AD_CUSTOMIZER_PRICE' => null,
        'AD_CUSTOMIZER_DATE' => null,
        'AD_CUSTOMIZER_STRING' => null,
    );
    $feedAttributeIds = array(
        'AD_CUSTOMIZER_INTEGER' => 0,
        'AD_CUSTOMIZER_PRICE' => 0,
        'AD_CUSTOMIZER_DATE' => 0,
        'AD_CUSTOMIZER_STRING' => 0,
    );

    //=================================================================
    // add CampaignService,AdGroupService,AdGroupCriterionService,
    //=================================================================
    // CampaignService
    $campaignValues = createCampaign($accountId);
    foreach ($campaignValues as $campaignValue) {
        if ($campaignId === 0) {
            $campaignId = $campaignValue->campaign->campaignId;
        }
    }

    // AdGroupService
    $adGroupValues = createAdGroup($accountId, $campaignId);
    foreach ($adGroupValues as $adGroupValue) {
        if ($adGroupId === 0) {
            $adGroupId = $adGroupValue->adGroup->adGroupId;
        }
    }

    // AdGroupCriterionService
    $adGroupCriterionValues = createAdGroupCriterion($accountId, $campaignId, $adGroupId);

    //=================================================================
    // FeedFolderService
    //=================================================================
    // FeedFolderServiceSample ADD
    $feedFolderValues = $feedFolderService->addFeedFolder($accountId);
    foreach ($feedFolderValues as $feedFolderValue) {
        if ($feedFolderId === 0) {
            $feedFolderId = $feedFolderValue->feedFolder->feedFolderId;
        }
        if(is_null($feedFolderName)){
            $feedFolderName = $feedFolderValue->feedFolder->feedFolderName;
        }
        foreach ($feedFolderValue->feedFolder->feedAttribute as $feedAttributeKey => $feedAttributeValue){
            if(is_null($feedAttributeNames[$feedAttributeValue->placeholderField])){
                $feedAttributeNames[$feedAttributeValue->placeholderField] = $feedAttributeValue->feedAttributeName;
            }
            if($feedAttributeIds[$feedAttributeValue->placeholderField] === 0){
                $feedAttributeIds[$feedAttributeValue->placeholderField] = $feedAttributeValue->feedAttributeId;
            }
        }
    }

    // FeedFolderServiceSample SET
    $feedFolderService->setFeedFolder($accountId, $feedFolderValues);

    // FeedFolderServiceSample GET
    $feedFolderService->getFeedFolder($accountId, $feedFolderValues);

    //=================================================================
    // FeedItemService
    //=================================================================
    // FeedItemServiceSample(AD_CUSTOMIZER) ADD
    $feedItemValues = $feedItemService->addFeedItem($accountId, $campaignId, $adGroupId, $feedFolderId, $feedAttributeIds);

    // FeedItemServiceSample(AD_CUSTOMIZER) SET
    $feedItemService->setFeedItem($accountId, $feedAttributeIds, $feedItemValues);

    // FeedItemServiceSample GET
    $feedItemService->getFeedItem($accountId, $feedItemValues);

    //=================================================================
    // add AdGroupAdService
    //=================================================================
    $adGroupAdValues = createAdGroupAd($accountId, $campaignId, $adGroupId, $feedFolderName, $feedAttributeNames);

    //=================================================================
    // remove AdGroupAdService,FeedItemService,FeedFolderService,
    // AdGroupCriterionService,AdGroupService,CampaignService
    //=================================================================
    // AdGroupAdService
    removeAdGroupAd($accountId, $campaignId, $adGroupId, $adGroupAdValues);

    // FeedItemService
    $feedItemService->removeFeedItem($accountId, $feedItemValues);

    // FeedFolderService
    $feedFolderService->removeFeedFolder($accountId, $feedFolderValues);

    // AdGroupCriterionService
    removeAdGroupCriterion($accountId, $campaignId, $adGroupId, $adGroupCriterionValues);

    // AdGroupService
    removeAdGroup($accountId, $campaignId, $adGroupValues);

    // CampaignService
    removeCampaign($accountId, $campaignValues);

} catch (Exception $e) {
    printf($e->getMessage() . "\n");
}