# Introduction #

## What is XAPR ? ##

XAPR, pronounced 'zapper', stands for XML and PHP Remoting and is an RPC library that uses PHP5’s XML functionality to handle requests from a variety of client technologies including Flash, Javascript and even PHP. XAPR is an alternative to other mechanisms such as SOAP, JSON-RPC and XML-RPC. Heavily influenced by AMFPHP, JSON-RPC and the excellent (but now seemingly abandoned) JPSPAN, XAPR is an attempt to take the best ideas and roll a universal solution.

## How does XAPR work? ##

XAPR sends and receives XML using HTTP POST. Scalar values like strings, integers and arrays, and custom classes are serialized into and deserialized from XML to allow a seamless transfer of data between the client and server. On the server, the developer creates PHP classes to act as Services and sets up a Gateway script to act as the endpoint from which to access them from a client. The client makes a call to the Gateway on the server, providing the name of the service, the method to call and any arguments required. The client XAPR library translates the call and serializes the arguments into XML before sending this request to the server. Once received, the Gateway deserializes the request XML and loads the appropriate Service class. The service method is then called using any arguments passed in the request. The result of the Service method is then serialized back into XML and returned to the client. When the client receives the XML response from the Gateway, the result is automatically deserialized from XML into native datatypes so the data is immediately usable.


## What does the XML look like? ##

Requests, Responses and Errors are fully formed XML documents.

### Sample Request ###

Each Request has a unique identifier that is returned as part of the response so that a callback function can be mapped to handle the results from the remote procedure call.

```
<?xml version="1.0"?>
<request id="0903233" version="0.1" method="TestService.echoString">
  <params>
    <a>
      <e k="0"><s><![CDATA[Hello World]]></s></e>
    <a>
  </params>
</request>
```

### Sample Response ###

```
<?xml version="1.0"?>
<response id="0903233" version="0.1">
  <result>
    <s><![CDATA[Hello World]]></s>
  </result>
</response>
```

### Sample Error ###

```
<?xml version="1.0"?>
<error id="0903233" version="0.1" code="" type="" msg="">
  <detail>
    <![CDATA[
      <?xml version="1.0"?>
      <request id="0903233" version="0.1" method="TestService.echoString">
        <params>
          <a>
            <e k="0"><s><![CDATA[Hello World]]></s></e>
          </a>
        </params>
     </request>
    ]]>
  </detail>
</error>
```

## XML Serialization ##

| **Data Type** | **XML Representation** |
|:--------------|:-----------------------|
| Boolean | `<b v="1" /> or <b v="false" />` |
| Number | `<n v="1" />`, `<n v="0.322" />` or `<n v="-3" />` |
| String | `<s><![CDATA[Some Text]]></s>` |
| Date  | `<d v="1205268912" />` for 03/11/2008 @ 4:55pm |
| Null/Undefined | `<u />` |
| Array (Mixed) | `<a><e k="0"><s><![CDATA[Item One]]></s></e><e k="1"><n v="1" /></e><e k="2"><b v="0" /></e></a>` |
| Object (Native) | `<o><p n="id"><n v="233" /></p><p n="published"><b v=”true” /></p><p n="tags"><s><![CDATA[rpc, xml, serialization]]></s></p></o>` |
| XML | `<x><![CDATA[<?xml version="1.0"?><root><sub /></root>]]></x>` |
| Object (Custom) | `<o c="Person"><p n="name"><s><![CDATA[Stephen]]></s></p><p n="age"><n v="30" /></p><p n="languages"><a><e k="0"><s><![CDATA[PHP]]></s></e><e ="1"><s><![CDATA[Actionscript]]></s></e><e k="2"><s><![CDATA[Javascript]]></s></e></a></p></o>` |


### Looks pretty complicated…is XAPR easy to use? ###

XAPR makes RPC simple by hiding away the XML serialization and deserialization. This allows client and server-side code to stay clean and developers to stay productive.

## System Requirements ##

PHP 5.2 or higher, with DOM and cURL libraries.