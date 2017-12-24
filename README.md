# abtest2

This extension supports TYPO3 administrators in performing A/B tests. This is useful when a site owner want to measure whether a new version improves or reduces user interaction compared to the current version.

Page properties get a new field "B Page" where you can provide the alternative page version. If the page is requested by the user, the extension checks wheter there is a B version specified. If this is the case, the version is selected by random. A cookie is set that remembers which version the user got (so there is no flip-flop if the user requests the page repeatedly). Once the cookie expires, the user is back to random at the next request.

Additional header information may be specified both for the original version as well as for the B version. This allows to track version differences in a web analysis tool such as Analytics. 


#### Example for Google Analytics:

In Google Analytics you can different your A and B site with 2 segments.

First you have to add a "Custom Definition" under your property settings.
Create a "Custom Dimension" with name "Variant A or B" for example. Set "Scope" to "Hit".
Then create a new advanced segment for your data and give it a segment name. Under advanced conditions add 2 filters.
1. filter: "Page" - "exactly matches" - "/mysite.html"
2. filter: "Custom Dimensions" (choose your previously created dimension "Variant A or B") - "contains" - "Variant B"

##### Additional Header Information:
```javascript
<script>
ga('send', 'pageview', { 'dimension1':  'Variant B' });
</script>
```
(dimension1 is the index of your dimension)

#### Demo

![Demo](https://github.com/svewap/abtest2/raw/master/Resources/Public/Images/Demo/demo.gif)