<?php
declare(strict_types = 1);

function weird_export(array $arr)
{
    $ret = "array (\n";
    foreach ($arr as $name => $val) {
        if ($name === "CURLOPT_URL") {
            // specialcase
            $url = $val[0];
            $args = $val[1] ?? null;
            if (empty($args)) {
                $ret .= "    CURLOPT_URL => " . var_export($url, true) . ",\n";
            } else {
                $ret .= "    CURLOPT_URL => " . var_export($url, true) . " . http_build_query(" . rtrim(var_export($args, true)) . "),\n";
            }
            continue;
        } else {
            $ret .= "    {$name} => " . var_export($val, true) . ",\n";
        }
    }
    $ret .= ")";
    return $ret;
}
if (empty($_POST['fiddler_raw'])) {
    echo <<<'HTML'
        <!-- -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <textarea id="input" style="width:100%;height:90%;" placeholder="paste fiddler here"></textarea>
        <pre id="result">result goes here..</pre>
    <script>
    $("#input").on("change input",function(){
        const raw = $("#input")[0].value.trim();
        if(raw.length<1){return;}
        $("#result")[0].textContent="loading...";
        let fd=new FormData();
        fd.append("fiddler_raw",raw);
        let xhr=new XMLHttpRequest();
        xhr.addEventListener("load",function(){
            $("#result")[0].textContent=xhr.responseText;
        });
        xhr.open("POST","?");
        xhr.send(fd);
    });
    </script>
    
    HTML;
    die();
}
$fiddler_raw = trim((string) $_POST['fiddler_raw']);
$header_end = strpos($fiddler_raw, "\r\n\r\n");
if ($header_end === false) {
    $headers_raw = $fiddler_raw;
    $body_raw = "";
} else {
    $headers_raw = substr($fiddler_raw, 0, $header_end);
    $body_raw = substr($fiddler_raw, $header_end + strlen("\r\n\r\n"));
}
$headers = array_values(array_filter(array_map("trim", explode("\n", $headers_raw)), 'strlen'));
$ret = [];
$request_method = explode(" ", $headers[0], 2)[0];
if ($request_method === "GET") {
    $ret["CURLOPT_HTTPGET"] = 1;
} elseif ($request_method === "POST") {
    $ret["CURLOPT_POST"] = 1;
} else {
    $ret["CURLOPT_CUSTOMREQUEST"] = $request_method;
}
$url = explode(" ", $headers[0])[1];
$question_pos = strpos($url, '?');
if ($question_pos === false) {
    $url_args = null;
    $ret["CURLOPT_URL"] = [
        $url
    ];
} else {
    $url_args = substr($url, $question_pos + strlen("?"));
    $url = substr($url, 0, $question_pos + strlen("?"));
    parse_str($url_args, $url_args);
    $ret["CURLOPT_URL"] = [
        $url,
        $url_args
    ];
}

// $http_version = explode(" ",$headers)[2]
// we could parse http/1.1 into CURLOPT_HTTP_VERSION=>CURL_HTTP_VERSION_1_1
// but i cba.
unset($headers[0]);
foreach ($headers as $header_key => $header_raw) {
    $header = explode(": ", $header_raw, 2);
    $name = $header[0];
    $name_lower = strtolower($name);
    $val = $header[1] ?? null;
    if ($name === "Host") {
        // ignored
        unset($headers[$header_key]);
        continue;
    }
    if ($name_lower === "accept-encoding" && ($val === null || false === stripos($val, "identity"))) {
        // specialcase CURLOPT_ENCODING
        $ret["CURLOPT_ENCODING"] = "";
        unset($headers[$header_key]);
        continue;
    }
    if ($name_lower === "user-agent") {
        // specialcase CURLOPT_USERAGENT
        $ret["CURLOPT_USERAGENT"] = $val;
        unset($headers[$header_key]);
        continue;
    }
    $ret["CURLOPT_HTTPHEADER"][] = $header_raw;
    unset($headers[$header_key]);
}
echo weird_export($ret);
die();
