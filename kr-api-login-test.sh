#!/usr/bin/env sh
# クライゼルのapiでログインできるかどうかテストする。
# SOAPなのでXMLを直接POSTで送っている

set -e
set -x

cd $(dirname $0)

KR_USER=${KR_USER:-username}
KR_PASSWORD=${KR_PASSWORD:-password}

set +x

if [ -f ./.env ]; then
    echo "read .env"
    . ./.env
fi

# curl -Ik https://krs.bz/rhd-itm/rpc
ENVELOPE=$(cat <<XML
<?xml version="1.0" encoding="UTF-8"?>
<env:Envelope 
  xmlns:env="http://www.w3.org/2003/05/soap-envelope" 
  xmlns:ns1="https://krs.bz/rpc" 
  xmlns:xsd="http://www.w3.org/2001/XMLSchema"
  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
  xmlns:enc="http://www.w3.org/2003/05/soap-encoding">
  <env:Body>
    <ns1:loginSession 
  	env:encodingStyle="http://www.w3.org/2003/05/soap-encoding">
      <param0 xsi:type="xsd:string">${KR_USER}</param0>
      <param1 xsi:type="xsd:string">${KR_PASSWORD}</param1>
    </ns1:loginSession>
  </env:Body>
</env:Envelope>
XML
)

echo $ENVELOPE | curl -X POST \
  -H "Connection: Keep-Alive" \
  -H "Host: krs.bz" \
  -H "Cookie: Cookie-Check=1;" \
  -H 'Content-Type: application/soap+xml; charset=utf-8; action="https://krs.bz/rpc#loginSession"' \
  -d @- "https://krs.bz/rhd-itm/rpc"
