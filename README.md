# abtest TYPO3 Extension

Extension for A/B-Tests

This extension supports TYPO3 administrators in performing A/B tests. This is useful when a site owner want to measure whether a new version improves or reduces user interaction compared to the current version.

### Features of the extension

1. Caching of each page version
2. A real 50/50% chance. That means: No selection by random, because of the unreliable random method. So the versions are always taken alternately.
3. Complete different content with same page id. So only one URL for two versions. The displayed version is determined by the cookie value.

#### More information

Page properties get a new field "B Page" where you can provide the alternative page version. If the page is requested by the user, the extension checks wheter there is a B version specified. If this is the case, the version is selected by "random". A cookie is set that remembers which version the user got (so there is no flip-flop if the user requests the page repeatedly). Once the cookie expires, the user is back to random at the next request.

Additional header information may be specified both for the original version as well as for the B version. This allows to track version differences in a web analysis tool such as Analytics. 


#### Demo

![Demo](https://raw.githubusercontent.com/werkraum-media/abtest/master/Documentation/Images/demo.gif)

