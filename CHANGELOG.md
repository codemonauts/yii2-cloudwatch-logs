# Yii2 Cloudwatch Logs Target Changelog

## [1.0.5] 2019-09-14
### Added
- Add this changelog.
- Documentation of sequence token rate limit.
### Changed
- Change version dependency to Yii2 framework.
 
## [1.0.4] - 2019-04-09
### Changed
- Only request log group, stream and sequence token when a log batch will be send. This avoids rate limits on AWS Cloudwatch.

## [1.0.3] - 2019-03-21
### Fixed
- Do not try to get the instance ID when not running on an instance. 

## [1.0.2] - 2019-03-10
### Changed
- Change version number.

## [1.0.1] - 2019-03-09
### Fixed
- Remove debugging code.

## [1.0.0] - 2019-03-08
### Added
- Initial release
