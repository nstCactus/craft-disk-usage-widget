# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## Unreleased


## 1.3.1 - 2022-11-16

### Fixed
- Fix PHP 7.2 compatibility issues


## 1.3.0 - 2022-11-05

### Added
- Add support for UNIX quotas

### Fixed
- Fix an error if the directory to watch doesn't exist


## 1.2.0 - 2022-09-27

### Added
- Add the ability to customize the soft limit: once this amount of disk usage is
  reached the indicator will turn red ([#1](https://github.com/nstCactus/craft-disk-usage-widget/issues/1))

### Fixed
- Fix disk usage indicator style


## 1.1.0 - 2022-09-14
### Changed
- Default monitored directory set to the Craft CMS base directory


## 1.0.0 - 2022-09-05
### Added
- Initial release
