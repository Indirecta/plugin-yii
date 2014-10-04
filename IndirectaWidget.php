<?php

/*
 * Example:
 * <?php $this->widget('IndirectaWidget', array(
 *     'siteid'=>'<YOUR SITEID>',
 *     'markup'=>'titles',
 *     'title'=>'Related articles',
 *     'id'=>'http://www.articles.com/best-article-ever.htm',
 * )); ?>
 *
 */

class IndirectaWidget extends CWidget {

    protected $api = 'http://indirecta.net/api/';

    public $title = 'Related articles';

    public $id;
    public $siteid;
    public $path = '/recom/item';
    public $markup = 'default';
    public $n = 4;

    public function run() {
        $pkid = $this->getPKID($this->siteid);

        $request  = $this->api;
        $request .= $this->siteid;
        $request .= $this->path;
        $request .= '?_id=' . $pkid;
        $request .= '&id=' . $this->id;
        $request .= '&n=' . $this->n;
        $request .= '&markup=' . $this->markup;
        $request .= '&r=' . substr(rand(), 2, 8);

        $client = curl_init($request);
        if ($client) {
            curl_setopt($client, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($client, CURLOPT_HEADER, 1);
            curl_setopt($client, CURLOPT_CONNECTTIMEOUT, 1);
            curl_setopt($client, CURLOPT_TIMEOUT, 1);
            $response = curl_exec($client);
            $http_code = curl_getinfo($client, CURLINFO_HTTP_CODE);
            if ($http_code == 200) {
                $size = curl_getinfo($client, CURLINFO_HEADER_SIZE);
                $parsed = json_decode(substr($response, $size));
                if ($parsed && $parsed->{'data'}) {
                    echo '<h3>' . $this->title . '</h3>' . $parsed->{'data'};
                }
            }
            curl_close($client);
        }
    }

    protected function getPKID($siteid) {
        $baseUrl = Yii::app()->getBaseUrl(true);
        $domainAlias = parse_url($baseUrl, PHP_URL_HOST);
        $domainHash = substr(sha1($domainAlias . '/'), 0, 4);

        $infoCookieKeys = array('_pk_id', $siteid, $domainHash);
        $infoCookie = implode('_', $infoCookieKeys);
        $infoValue = isset($_COOKIE[$infoCookie]) ? $_COOKIE[$infoCookie] : '';

        if (empty($infoValue)) {
            $now  = time();
            $pkid = substr(sha1(Yii::app()->getSession()->getSessionId()), 0, 16);

            $infoCookie  = implode('.', $infoCookieKeys);
            $infoValue = implode('.', array($pkid, $now, '0', '0', '0', '0'));

            setcookie($infoCookie, $infoValue, $now + 63072000, '/');

            $infoCookie = implode('_', $infoCookieKeys);
            $_COOKIE[$infoCookie] = $infoValue;
        }

        if (empty($pkid)) {
            $values = array_values(explode('.', $infoValue));
            $pkid = $values[0];
        }

        return $pkid;
    }
}
