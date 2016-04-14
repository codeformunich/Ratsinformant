<?php
$I = new OparlTester($scenario);
$I->wantTo('get the oparl:term list');
$I->sendGET('/body/0/list/term');
$I->seeOparl('
{
  "items": [
    {
      "type": "https://oparl.org/schema/1.0/Legis­lat­iveTerm",
      "name": "Unbekannt",
      "startDate": "0000-00-00",
      "endDate": "0000-00-00"
    },
    {
      "type": "https://oparl.org/schema/1.0/Legis­lat­iveTerm",
      "name": "1996-2002",
      "startDate": "1996-12-03",
      "endDate": "2002-12-03"
    },
    {
      "type": "https://oparl.org/schema/1.0/Legis­lat­iveTerm",
      "name": "2002-2008",
      "startDate": "2002-12-03",
      "endDate": "2008-12-03"
    },
    {
      "type": "https://oparl.org/schema/1.0/Legis­lat­iveTerm",
      "name": "2008-2014",
      "startDate": "2008-12-03",
      "endDate": "2014-12-03"
    },
    {
      "type": "https://oparl.org/schema/1.0/Legis­lat­iveTerm",
      "name": "2014-2020",
      "startDate": "2014-12-03",
      "endDate": "2020-12-03"
    }
  ],
  "itemsPerPage": 100,
  "firstPage": "http://localhost:8080/oparl/v1.0/body/0/list/term",
  "lastPage": "http://localhost:8080/oparl/v1.0/body/0/list/term",
  "numberOfPages": 1
}
');
