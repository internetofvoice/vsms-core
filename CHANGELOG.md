# Changelog

Changes are documented in reverse chronological order.

## Version 1.4.0 (2019-05-30)
* Switch to libvoice 1.3.0 and PHPUnit 7     

## Version 1.3.3 (2019-04-01)
* Extend SkillHelper::extractAmazonDate to recognize XXXX-XX-01 and 2019-01-XX date values     

## Version 1.3.2 (2019-03-11)
* Extend LogHelper::logRequest to optionally not include request headers  

## Version 1.3.1 (2019-02-19)
* Add support for future dates in SkillHelper::convertDateTimeToHuman()  

## Version 1.3.0 (2019-02-17)
* Support variants in TranslationHelper (multiple translations, pick one by random)  

## Version 1.2.4 (2018-12-16)
* Switch to [analog-stable](https://github.com/jbroadway/analog) library

## Version 1.2.3 (2018-01-21)
* Fix bug in extraction of AMAZON.DATE slot type 
* Extend exception handling
* Improve test coverage

## Version 1.2.2 (2017-12-19)
* Extend exception handling

## Version 1.2.1 (2017-10-11)
* Add separate prefix for today / yesterday to SkillHelper->convertDateTimeToHuman()

## Version 1.2.0 (2017-10-11)
* Switch to [LibVoice](https://github.com/internetofvoice/libvoice) library

## Version 1.0.1 (2017-09-27)
* Add exception handling

## Version 1.0.0 (2017-09-11)
* First public release
