# Running a server

*This page is written as the server lands (roadmap phase 5).* It will show how
to construct the PSR-15 handler with the in-memory stores, mount it under a
base path, publish a first logistics object through the PHP API, and then
replace each in-memory store with your own implementation of the
[SPI](../guide/spi.md).
