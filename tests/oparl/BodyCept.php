<?php
$I = new OparlTester($scenario);
$I->wantTo('get two oparl:body objects (the Stadtrat and one BA)');
$I->sendGET('/body/0');
$I->seeOparl('
{
  "id": "http://localhost:8080/oparl/v1.0/body/0",
  "type": "https://oparl.org/schema/1.0/Body",
  "system": "http://localhost:8080/oparl/v1.0",
  "contactEmail": "info@muenchen-transparent.de",
  "contactName": "München Transparent",
  "name": "Stadrat der Landeshauptstadt München",
  "shortName": "Stadtrat",
  "website": "http://www.muenchen.de/",
  "organization": "http://localhost:8080/oparl/v1.0/body/0/organizations",
  "person": "http://localhost:8080/oparl/v1.0/body/0/persons",
  "meeting": "http://localhost:8080/oparl/v1.0/body/0/meetings",
  "paper": "http://localhost:8080/oparl/v1.0/body/0/papers",
  "terms": "http://localhost:8080/oparl/v1.0/body/0/terms"
}
');
/*$I->sendGET('/body/1');
$I->seeOparl('
{
  "id": "http://localhost:8080/oparl/v1.0/body/1",
  "type": "https://oparl.org/schema/1.0/Body",
  "system": "http://localhost:8080/oparl/v1.0",
  "contactEmail": "info@muenchen-transparent.de",
  "contactName": "München Transparent",
  "name": "Bezirksausschuss 1: BA mit Ausschuss mit Termin",
  "shortName": "BA 1",
  "website": "http://localhost:8080/bezirksausschuss/1_BA+mit+Ausschuss+mit+Termin",
  "organization": "http://localhost:8080/oparl/v1.0/body/1/organizations",
  "person": "http://localhost:8080/oparl/v1.0/body/1/persons",
  "meeting": "http://localhost:8080/oparl/v1.0/body/1/meetings",
  "paper": "http://localhost:8080/oparl/v1.0/body/1/papers",
  "terms": "http://localhost:8080/oparl/v1.0/body/1/terms"
}
');*/
