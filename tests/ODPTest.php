<?php

namespace DataSift\Tests;

use DataSift_ODP;
use DataSift_User;

class OdpTest extends \PHPUnit_Framework_TestCase
{
    protected $user = false;
    protected $source_id = false;
    protected $odp = false;

    protected function setUp()
    {
        require_once(dirname(__FILE__) . '/../lib/datasift.php');
        require_once(dirname(__FILE__) . '/../config.php');
        $this->user = new DataSift_User(USERNAME, API_KEY);
        $this->user->setApiClient('\DataSift\Tests\MockApiClient');
        MockApiClient::setResponse(false);
    }

    public function testSourceLength()
    {
        $odp = new DataSift_ODP($this->user, '1f1be6565a1d4ef38f9f4aeec9554440');

        $this->assertEquals(
            $odp->getSourceId(),
            '1f1be6565a1d4ef38f9f4aeec9554440',
            'Hash does not meet the required length'
        );
    }

    public function testNoSource()
    {
        $odp = new DataSift_ODP($this->user, '');

        $this->setExpectedException('DataSift_Exception_InvalidData');

        $data_set = '[{"id": "234", "body": "yo"}]';

        $odp->ingest($data_set);
    }

    public function testNoData()
    {
        $odp = new DataSift_ODP($this->user, '1f1be6565a1d4ef38f9f4aeec9554440');

        $this->setExpectedException('DataSift_Exception_InvalidData');

        $data_set = '';

        $odp->ingest($data_set);
    }

    public function testIngest()
    {
        $response = array(
            'response_code' => 200,
            'data' => array(
                'accepted' => 1,
                'total_message_bytes' => 1788,
            ),
            'rate_limit' => 200,
            'rate_limit_remaining' => 150,
        );

        MockApiClient::setResponse($response);

        $data_set = '[{"id": "234", "body": "yo"}]';

        $source_id = '1f1be6565a1d4ef38f9f4aeec9554440';

        $ingest = new DataSift_ODP($this->user, $source_id);
        $response = $ingest->ingest($data_set);

        $this->assertEquals($response['accepted'], 1, 'Not accepted');
    }
}
