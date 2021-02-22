# TecArt Ticket System

A front-end for the [TecArt Business Software Trouble Ticket System](https://www.tecart.de/trouble-ticket-interface).


------------------------------------------------------------------------------- 

TecArt is a registered trademark in Germany and a registered EU trade mark.

TecArt ist ein registriertes Markenzeichen in Deutschland und eine 
registrierte Unionsmarke.


----

Usage with Active Directory (LDAP):

If enabled and a user tries to authenticate with the TecArt Ticket System, it will 
first search for a contact which has the username stored in ad_username_field. If 
a contact is found the ticket system will try to authenticate the user on base of
the DN stored in the contact and the specified username and password.   

The configuration in config/config.php can contain multi ad-specific parameters.
Currently only one AD-Domain is allowed. The so called account-suffix is automatically created by the DN stored in the ad_dn_field.

- use_ad: (boolean) enables the authentication with an Active Directory
- ad_username_field: (string) the crm user defined field in contacts which holds the holds the username  
- ad_dn_field: (string) the crm user defined field in contacts which holds the holds the DN (Distinguished Name)
- ad_domain: (string) the address of the AD-Server
- ad_port: (string) the port of the AD-Server
- ad_use_ssl: (boolean) disables or enables the usage of SSL
- ad_use_tls: (boolean) disables or enables the usage of TLS