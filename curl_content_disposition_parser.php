<?php
declare(strict_types = 1);
$raw = trim(getRaw());
$lines = explode("\n", $raw);
$parsed = [];
$separator = $lines[0];
$current = [
    "name" => null,
    "value" => null
];
$isInHeaders = true;
for ($linenum = 1; $linenum < count($lines); ++ $linenum) {
    $matches = [];
    $line = $lines[$linenum];
    if ($isInHeaders && preg_match('/^Content\-Disposition\:\ form\-data\;\ name\=\"(?<name>.*)\"\s*$/', $line, $matches)) {
        $name = $matches["name"];
        $isArray = (substr($name, - 2) === "[]");
        $current["isArray"] = $isArray;
        if ($isArray) {
            $name = substr($name, 0, - 2);
        }
        $current["name"] = $name;
        continue;
    }
    if ($isInHeaders && (in_array($line, array(
        "",
        "\r",
        "\n",
        "\r\n"
    ), true))) {
        $isInHeaders = false;
        continue;
    }
    if ($isInHeaders) {
        var_dump($line);
        throw new \LogicException("did not understand header!");
    }

    if ($line === $separator || $line === $separator . '--') {
        // echo("FOUND A SEPARATOR!");
        if ($current["isArray"]) {
            $realname = (function () use (&$parsed, &$current): string {
                for ($i = 0;; ++ $i) {
                    $testname = $current["name"] . "[{$i}]";
                    if (! isset($parsed[$testname])) {
                        return $testname;
                    }
                }
            })();
            $parsed[$realname] = $current["value"];
        } else {
            $parsed[$current["name"]] = $current["value"];
        }
        if ($line === $separator . '--') {
            // found the very last line! done parsing
            break;
        }
        $current = [
            "name" => null,
            "value" => null,
            "isArray" => null
        ];
        $isInHeaders = true;
        continue;
    }
    // guess we're in a value
    $current["value"] .= $line;
}
var_export($parsed);

function getRaw()
{
    $raw = <<<'RAW'
    -----------------------------199517534631555257471919811716
    Content-Disposition: form-data; name="subscriberId"
    
    
    -----------------------------199517534631555257471919811716
    Content-Disposition: form-data; name="name"
    
    Testnavn
    -----------------------------199517534631555257471919811716
    Content-Disposition: form-data; name="company"
    
    Testfirma
    -----------------------------199517534631555257471919811716
    Content-Disposition: form-data; name="orgnr"
    
    1234
    -----------------------------199517534631555257471919811716
    Content-Disposition: form-data; name="address"
    
    1234vei
    -----------------------------199517534631555257471919811716
    Content-Disposition: form-data; name="postnr"
    
    1234
    -----------------------------199517534631555257471919811716
    Content-Disposition: form-data; name="place"
    
    Testpoststed
    -----------------------------199517534631555257471919811716
    Content-Disposition: form-data; name="cellphone"
    
    999999999
    -----------------------------199517534631555257471919811716
    Content-Disposition: form-data; name="new_password"
    
    test
    -----------------------------199517534631555257471919811716
    Content-Disposition: form-data; name="subscriberEmail"
    
    test4@test.test
    -----------------------------199517534631555257471919811716
    Content-Disposition: form-data; name="widgetId"
    
    40
    -----------------------------199517534631555257471919811716
    Content-Disposition: form-data; name="countryid"
    
    1
    -----------------------------199517534631555257471919811716
    Content-Disposition: form-data; name="userlang"
    
    1
    -----------------------------199517534631555257471919811716
    Content-Disposition: form-data; name="statusCodeId"
    
    1
    -----------------------------199517534631555257471919811716
    Content-Disposition: form-data; name="invoiceemail"
    
    
    -----------------------------199517534631555257471919811716
    Content-Disposition: form-data; name="invoicedays"
    
    20
    -----------------------------199517534631555257471919811716
    Content-Disposition: form-data; name="emailreport"
    
    0
    -----------------------------199517534631555257471919811716
    Content-Disposition: form-data; name="emailreportOld"
    
    
    -----------------------------199517534631555257471919811716
    Content-Disposition: form-data; name="newsletter"
    
    0
    -----------------------------199517534631555257471919811716
    Content-Disposition: form-data; name="livereport"
    
    0
    -----------------------------199517534631555257471919811716
    Content-Disposition: form-data; name="reportemails"
    
    
    -----------------------------199517534631555257471919811716
    Content-Disposition: form-data; name="accountType"
    
    0
    -----------------------------199517534631555257471919811716
    Content-Disposition: form-data; name="parent"
    
    
    -----------------------------199517534631555257471919811716
    Content-Disposition: form-data; name="nameForParent"
    
    
    -----------------------------199517534631555257471919811716
    Content-Disposition: form-data; name="companyForParent"
    
    
    -----------------------------199517534631555257471919811716
    Content-Disposition: form-data; name="accounttype"
    
    -1
    -----------------------------199517534631555257471919811716
    Content-Disposition: form-data; name="widget"
    
    -1
    -----------------------------199517534631555257471919811716
    Content-Disposition: form-data; name="userid"
    
    0
    -----------------------------199517534631555257471919811716
    Content-Disposition: form-data; name="crmcustomerid"
    
    0
    -----------------------------199517534631555257471919811716
    Content-Disposition: form-data; name="invoicecustomerid"
    
    0
    -----------------------------199517534631555257471919811716
    Content-Disposition: form-data; name="moderation"
    
    0
    -----------------------------199517534631555257471919811716
    Content-Disposition: form-data; name="targetcode"
    
    
    -----------------------------199517534631555257471919811716
    Content-Disposition: form-data; name="facebookid"
    
    
    -----------------------------199517534631555257471919811716
    Content-Disposition: form-data; name="instagram_userid"
    
    
    -----------------------------199517534631555257471919811716
    Content-Disposition: form-data; name="publish_facebook_post"
    
    0
    -----------------------------199517534631555257471919811716
    Content-Disposition: form-data; name="api"
    
    
    -----------------------------199517534631555257471919811716
    Content-Disposition: form-data; name="externalid"
    
    
    -----------------------------199517534631555257471919811716
    Content-Disposition: form-data; name="externalid2"
    
    
    -----------------------------199517534631555257471919811716
    Content-Disposition: form-data; name="invoiceinfoid"
    
    0
    -----------------------------199517534631555257471919811716
    Content-Disposition: form-data; name="notes"
    
    
    -----------------------------199517534631555257471919811716
    Content-Disposition: form-data; name="removeCreditPoints"
    
    
    -----------------------------199517534631555257471919811716
    Content-Disposition: form-data; name="digitalnew"
    
    0
    -----------------------------199517534631555257471919811716
    Content-Disposition: form-data; name="digitalstart"
    
    2021-11-03
    -----------------------------199517534631555257471919811716
    Content-Disposition: form-data; name="addCreditPoints"
    
    0
    -----------------------------199517534631555257471919811716
    Content-Disposition: form-data; name="addCreditPointsDesc"
    
    admincredits
    -----------------------------199517534631555257471919811716
    Content-Disposition: form-data; name="comWidgetid[]"
    
    0
    -----------------------------199517534631555257471919811716
    Content-Disposition: form-data; name="comLevel1[]"
    
    
    -----------------------------199517534631555257471919811716
    Content-Disposition: form-data; name="comThreshold1[]"
    
    
    -----------------------------199517534631555257471919811716
    Content-Disposition: form-data; name="comLevel2[]"
    
    
    -----------------------------199517534631555257471919811716
    Content-Disposition: form-data; name="new"
    
    1
    -----------------------------199517534631555257471919811716
    Content-Disposition: form-data; name="form"
    
    [object HTMLFormElement]
    -----------------------------199517534631555257471919811716--
    
    RAW;
    return $raw;
    echo $raw;
    echo hash("sha1", $raw, false), "\n";
}
