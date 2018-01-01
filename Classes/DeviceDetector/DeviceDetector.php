<?php
/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */

namespace WapplerSystems\ABTest2\DeviceDetector;

use TYPO3\CMS\Core\Utility\DebugUtility;
use WapplerSystems\ABTest2\DeviceDetector\Parser\Bot;
use WapplerSystems\ABTest2\DeviceDetector\Yaml\Parser AS YamlParser;
use WapplerSystems\ABTest2\DeviceDetector\Yaml\Spyc;

/**
 * Class DeviceDetector

 *
 * @package DeviceDetector
 */
class DeviceDetector
{
    /**
     * Constant used as value for unknown browser / os
     */
    const UNKNOWN = "UNK";

    /**
     * Holds the useragent that should be parsed
     * @var string
     */
    protected $userAgent;

    /**
     * Holds bot information if parsing the UA results in a bot
     * (All other information attributes will stay empty in that case)
     *
     * If $discardBotInformation is set to true, this property will be set to
     * true if parsed UA is identified as bot, additional information will be not available
     *
     * If $skipBotDetection is set to true, bot detection will not be performed and isBot will
     * always be false
     *
     * @var array|boolean
     */
    protected $bot = null;

    /**
     * @var bool
     */
    protected $discardBotInformation = false;

    /**
     * @var bool
     */
    protected $skipBotDetection = false;


    /**
     * Holds the parser class used for parsing yml-Files
     * @var \WapplerSystems\ABTest2\DeviceDetector\Yaml\Parser
     */
    protected $yamlParser = null;


    /**
     * @var bool
     */
    private $parsed = false;

    /**
     * Constructor
     *
     * @param string $userAgent UA to parse
     */
    public function __construct($userAgent = '')
    {
        if ($userAgent != '') {
            $this->setUserAgent($userAgent);
        }

    }

    /**
     * Sets the useragent to be parsed
     *
     * @param string $userAgent
     */
    public function setUserAgent($userAgent)
    {
        if ($this->userAgent != $userAgent) {
            $this->reset();
        }
        $this->userAgent = $userAgent;
    }

    protected function reset()
    {
        $this->bot = null;
        $this->parsed = false;
    }



    /**
     * Sets whether to discard additional bot information
     * If information is discarded it's only possible check whether UA was detected as bot or not.
     * (Discarding information speeds up the detection a bit)
     *
     * @param bool $discard
     */
    public function discardBotInformation($discard = true)
    {
        $this->discardBotInformation = $discard;
    }

    /**
     * Sets whether to skip bot detection.
     * It is needed if we want bots to be processed as a simple clients. So we can detect if it is mobile client,
     * or desktop, or enything else. By default all this information is not retrieved for the bots.
     *
     * @param bool $skip
     */
    public function skipBotDetection($skip = true)
    {
        $this->skipBotDetection = $skip;
    }

    /**
     * Returns if the parsed UA was identified as a Bot
     *
     * @see bots.yml for a list of detected bots
     *
     * @return bool
     */
    public function isBot()
    {
        return !empty($this->bot);
    }


    /**
     * Returns the user agent that is set to be parsed
     *
     * @return string
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * Returns the bot extracted from the parsed UA
     *
     * @return array
     */
    public function getBot()
    {
        return $this->bot;
    }

    /**
     * Returns true, if userAgent was already parsed with parse()
     * 
     * @return bool
     */
    public function isParsed()
    {
        return $this->parsed;
    }

    /**
     * Triggers the parsing of the current user agent
     * @throws \Exception
     */
    public function parse()
    {
        if ($this->isParsed()) {
            return;
        }

        $this->parsed = true;

        // skip parsing for empty useragents or those not containing any letter
        if (empty($this->userAgent) || !preg_match('/([a-z])/i', $this->userAgent)) {
            return;
        }

        $this->parseBot();
        if ($this->isBot()) {
            return;
        }

    }

    /**
     * Parses the UA for bot information using the Bot parser
     * @throws \Exception
     * @return void
     */
    protected function parseBot()
    {
        if ($this->skipBotDetection) {
            $this->bot = false;
            return;
        }

        $botParser = new Bot();
        $botParser->setUserAgent($this->getUserAgent());
        $botParser->setYamlParser($this->getYamlParser());
        if ($this->discardBotInformation) {
            $botParser->discardDetails();
        }
        $this->bot = $botParser->parse();

    }



    protected function matchUserAgent($regex)
    {
        $regex = '/(?:^|[^A-Z_-])(?:' . str_replace('/', '\/', $regex) . ')/i';

        if (preg_match($regex, $this->userAgent, $matches)) {
            return $matches;
        }

        return false;
    }



    /**
     * Sets the Yaml Parser class
     *
     * @param YamlParser
     * @throws \Exception
     */
    public function setYamlParser($yamlParser)
    {
        if ($yamlParser instanceof YamlParser) {
            $this->yamlParser = $yamlParser;
            return;
        }

        throw new \Exception('Yaml Parser not supported');
    }

    /**
     * Returns Yaml Parser object
     *
     * @return YamlParser
     */
    public function getYamlParser()
    {
        if (!empty($this->yamlParser)) {
            return $this->yamlParser;
        }

        return new Spyc();
    }
}
