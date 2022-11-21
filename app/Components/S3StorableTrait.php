<?php
/**
 * Created by PhpStorm.
 * User: konst
 * Date: 1/9/16
 * Time: 1:11 AM
 */

namespace App\Components;

trait S3StorableTrait
{
    /**
     * @var AwsS3Policy
     */
    protected static $policyGenerator;

    /**
     * @param null $key
     * @param int  $lifetime
     *
     * @return string
     */
    public function getS3Url($lifetime = 20, $key = null)
    {
        if ($key === null) {
            $key = $this->getS3key();
        }
        return $this->getPolicyGenerator()->getSignedUrl($key, $lifetime);
    }

    /**
     * @return AwsS3Policy
     */
    public static function getPolicyGenerator()
    {
        if (!self::$policyGenerator) {
            self::$policyGenerator = new AwsS3Policy();
        }
        return self::$policyGenerator;
    }
}