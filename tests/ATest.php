<?php
/**
 * Created by PhpStorm.
 * User: konst
 * Date: 1/8/16
 * Time: 6:17 PM
 */

namespace tests;


use App\Components\AwsS3Policy;

class ATest extends \TestCase
{
    /**
     * @var AwsS3Policy
     */
    protected $awsS3Policy;

    /**
     *
     */
    public function setUp()
    {
        parent::setUp();
        $this->awsS3Policy = new AwsS3Policy(
            'appraiserpal',
            'us-east-1',
            ['key' => 'AKIAJ23JF7NCZ7MLTZDA', 'secret' => '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'],
            ['key' => 'AKIAJWPMRLU4CQKV6ALA', 'secret' => '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']
        );
    }

    public function testPolicyGeneration()
    {
//        $policy = $this->awsS3Policy->createUploadPolicy(['filename' => []]);
//        $s3Client = new S3Client([
//            'region' => 'us-east-1',
//            'version' => 'latest',
//            'credentials' => [
//                'key' => 'AKIAJWPMRLU4CQKV6ALA',
//                'secret' => '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'
//            ]
//        ]);
//        $postObject = new PostObject($s3Client, 'appraiserpal', [], $policy);
//        $fields = $postObject->getFormInputs();
//        $result = $this->awsS3Policy->getUploadPolicyWithSignature(['filename' => '']);
////        $this->assertEquals($fields['policy'], $result['policy']);
//        $this->assertEquals($fields['signature'], $result['signature']);
        $a = [1, 3];
        $b = [4];
        $c = $a + $b;
        var_dump($c);
    }

}
