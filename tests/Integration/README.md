# Cloudy Integration Testing

## How to Test

1. Create a PHP class extending `\PHPUnit\Framework\TestCase`
1. Add a method that will test a single BASH file.
   
    ```php
    namespace AKlump\Cloudy\Tests\Integration;
    
    use AKlump\Cloudy\Tests\Integration\TestingTraits\TestWithCloudyTrait;
    use PHPUnit\Framework\TestCase;
    
    /**
     * @coversNothing
     */
    class ConfigTest extends TestCase {
    
      use TestWithCloudyTrait;
    
      public function testCanReadBaseConfig() {
        $output = $this->execCloudy('TitleTest.sh');
        $this->assertSame('Foo', $output);
      }
    
      protected function setUp(): void {
        $this->bootCloudy(__DIR__ . '/t/ConfigTest/base.yml');
      }
    
    }
    
    ```
2. Create a directory of the same filename inside _t/_
3. Create a YML file with the Cloudy app base config inside that folder; see _script.example.yml_

    ```
    tests/Integration/t
    └── ConfigTest
        ├── ColorTest.sh
        ├── TitleTest.sh
        ├── base.yml
        └── config
            └── additional.yml
    ```
           
4. Create a BASH file with the Cloudy code you wish to test.

    ```shell
    eval $(get_config_as 'title' 'title')
    echo $title
    ```

## Logging

The tests are logged to _tests.log_
