<?php
require_once('abstract.php');
/*
 * @author Stoyvo
 */
class Pools_CkpoolSolo extends Pools_Abstract {

    // Pool Information
    protected $_btcaddess;
    protected $_type = 'ckpool-solo';

    public function __construct($params) {
        parent::__construct(array('apiurl' => 'http://solo.ckpool.org'));
        $this->_btcaddess = $params['address'];
        $this->_fileHandler = new FileHandler('pools/' . $this->_type . '/'. hash('md4', $params['address']) .'.json');
    }

    public function update() {
        if ($GLOBALS['cached'] == false || $this->_fileHandler->lastTimeModified() >= 30) { // updates every 30 seconds
            $poolData = array();

            $poolStatus = explode(PHP_EOL, curlCall($this->_apiURL . '/pool/pool.status'));
            if (!empty($poolStatus[0])) {
                $poolData['pool']['general'] = json_decode($poolStatus[0], true);
            }
            if (!empty($poolStatus[1])) {
                $poolData['pool']['hashrate'] = json_decode($poolStatus[1], true);
            }
            if (!empty($poolStatus[2])) {
                $poolData['pool']['shares'] = json_decode($poolStatus[2], true);
            }
            $poolData['user'] = curlCall($this->_apiURL . '/users/' . $this->_btcaddess);

            // Offline Check
            if (empty($poolData['pool']) || empty($poolData['user'])) {
                return;
            }

            // Data Order
            $data['type'] = $this->_type;

            $data['user_hashrate'] = formatHashrate($this->normalizeHashrate($poolData['user']['hashrate5m']));
            $data['user_workers'] = $poolData['user']['workers'];
            $data['user_best_share'] = number_format($poolData['user']['bestshare'], 8);

            $data['pool_hashrate'] = formatHashrate($this->normalizeHashrate($poolData['pool']['hashrate']['hashrate5m']));
            $data['pool_workers'] = $poolData['pool']['general']['Workers'];
            $data['pool_users'] = $poolData['pool']['general']['Users'];
            $data['uptime'] = formatTimeElapsed($poolData['pool']['general']['runtime']);

            $data['url'] = $this->_apiURL;

            $this->_fileHandler->write(json_encode($data));
            return $data;
        }

        return json_decode($this->_fileHandler->read(), true);
    }

    private function normalizeHashrate($hashrate) {
        $lastChar = substr($hashrate, -1);
        $hashrate = rtrim($hashrate, $lastChar);

        switch($lastChar) {
            case 'M':
                $hashrate *= 1000;
                break;
            case 'G':
                $hashrate *= 1000000;
                break;
            case 'T':
                $hashrate *= 1000000000;
                break;
            case 'T':
                $hashrate *= 1000000000000;
                break;
        }

        return $hashrate;
    }

}
