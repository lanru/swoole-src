#!/bin/bash
export LDFLAGS="-L/usr/local/opt/openssl@1.1/lib"
export CPPFLAGS="-I/usr/local/opt/openssl@1.1/include"
export LDFLAGS="-L/usr/local/opt/libiconv/lib"
export CPPFLAGS="-I/usr/local/opt/libiconv/include"
./configure --with-openssl-dir=/usr/local/opt/openssl@1.1 --enable-http2 --enable-debug --enable-trace-log