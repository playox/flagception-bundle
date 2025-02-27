# Upgrade from 5.x to 6.0
Support for annotations has been removed. Use PHP 8 attributes instead. In addition, the Flagception SDK and Flagception Database Activator has been updated to version 2.0.
The updated SDK added some types for the interfaces. The updated Database Activator added support for DBAL 4.0 and dropped support for DBAL 3.5 and below.

## Upgrade steps
### Custom Activators
If you have created custom activators, you need to update interface implementations to match the new SDK version. Only types are added.

### Database Activator
The database activator now requires DBAL 3.6 or higher. If you are using DBAL 3.5 or below, you need to update your dependencies.
In addition, a PDO instance is no longer supported. You need to pass the connection options as an array, string or DBAL instance.

Have fun with Flagception :-)
