# PHP Annotations #
Use PHP5's powerful reflection API to mark classes for use with XAPR rather than having to extend from the base PHP service class.

# Self describing services #
Add functionality to automatically describe a service in XAPR format to aid the generation of client side proxies. Something like WSDL for SOAP or SMD for JSON-RPC.

# Authentication #
Provide authentication hooks on the server-side to support external schemes such as OAuth.

# Request/Response Design #
Redesign how request/response packets look so that they are valid XAPR markup and not special cases.
In addition, support bundled requests and responses so the total number of server requests can be minimised for a web application.

# Response caching #
Implement an event-based subsystem for XAPR on the server to support flexible caching for XAPR responses to regular requests.

# Data Referencing #
Support circular referencing and more efficient serialisation when data appears more than once in a request/response.

# Service Browser #
A demo application, working as a proxy gateway that shows data sent and received in a more user-friendly format (like Charles/Service Capture).

# More Languages #
Server-side libraries for HaXe and .NET/Mono.

# More examples #
Pageable, sortable, searchable datagrid example to demonstrate how custom classes can works as Value Objects with Command and Service Locator patterns on the client.