I will explain this in greater detail once it solidifies a bit.

```
<?xml version="1.0" encoding="ISO-8859-1"?>
<module>
    <version>2.0</version>
    <type>commands</type>
    <name>Areski CDR Stats</name>
    <credits>http://www.areski.net/asterisk-stat-v2/</credits>
    <description>Asterisk CDR Stats</description>
    <icon>mimetypes/ooo_calc</icon>
    <allow_multiple>false</allow_multiple>
    <menu>
        <item>
            <link>cdr/cdr.php</link>
            <use_iframe>1</use_iframe>
            <text>
                <en>CDRStats</en>
            </text>
            <security_level>1</security_level>
        </item>
    </menu>
    <config_options>
        <option>
                <name>cdr_webroot</name>
                <value>cdr/</value>
                <text>Areski CDR Web Root</text>
        </option>
        <option>
                <name>cdr_fsroot</name>
                <value>FSROOT/cdr/</value>
                <text>Areski CDR Web Root</text>
        </option>
        <option>
                <name>cdr_host</name>
                <value>localhost</value>
                <text>Areski CDR Web Root</text>
        </option>
        <option>
                <name>cdr_port</name>
                <value>3306</value>
                <text>Areski CDR Port</text>
        </option>
        <option>
                <name>cdr_user</name>
                <value>root</value>
                <text>Areski CDR Username</text>
        </option>
        <option>
                <name>cdr_pass</name>
                <value></value>
                <text>Areski CDR Pass</text>
        </option>
        <option>
                <name>cdr_db</name>
                <value>cdr</value>
                <text>Areski CDR DB</text>
        </option>
        <option>
                <name>cdr_db_type</name>
                <value>mysql</value>
                <text>Areski CDR DB Type</text>
        </option>
        <option>
                <name>cdr_table</name>
                <value>cdr</value>
                <text>Areski CDR Table</text>
        </option>
    </config_options>
    <patch>
        <base>cdr/lib/</base>
        <diff>
            LS0tIGRlZmluZXMucGhwCTIwMDktMTAtMDkgMTk6NTE6MzcuMDAwMDAwMDAwICsxMzAwCisrKyBk
            ZWZpbmVzLnBocAkyMDA5LTEwLTA5IDIwOjI5OjA1LjAwMDAwMDAwMCArMTMwMApAQCAtMSwyMiAr
            MSwyNSBAQAogPD9waHAKK3Nlc3Npb25fc3RhcnQoKTsKKyRjb25maWdfdmFsdWVzID0gJF9TRVNT
            ...
            VEFCTEVOQU1FIiwgJGNvbmZpZ192YWx1ZXNbJ2Nkcl90YWJsZSddKTsKICAKIC8vIFJlZ2FyZGlu
            ZyB0byB0aGUgZHN0IHlvdSBjYW4gc2V0dXAgYW4gYXBwbGljYXRpb24gbmFtZQogLy8gTWFrZSBt
            b3JlIHNlbnNlIHRvIGhhdmUgYSB0ZXh0IHRoYXQganVzdCBhIG51bWJlcgo=
        </diff>
    </patch>

    <archive>
        <filename>cdr.tar.gz</filename>
        <contents>
            H4sIAN6TzkoAA+w9aXfixrL5Cr+iozBjiBcEeMkMCB8M2OYGG18bZ96cyRyOANlWAhKRxDh+E//3
            V1XdkloLNt7G7+ZGZwb1Ul1VXV1dXb2oPRo7xe9e+FHh2dnaojc88TeFS6XydmWrpFY2N79T4VWu
            LZcqpXJ5a6uM7b+1s/MdU1+Al8Tjt/9/6ZPNZjPqDjtqnDYPWVlVt9h7dto+6Z322y22d37Aspl1
            ...
            SpP7kfPO1BO/zvHMzjqAyMcA7DcUX6/2/EgNP8eDDcdxZFDPRhB5bbyKakzwToF7VG/+ndXgU394
            6ct6sc+tn6utZJPtt17Dw1iVVHfoAqPHaOyhTtafMhgusmDEymjA8CCMrjEHo07v9zEmr2KC9yZ3
            Te1mvdncP2ycOuNJe/rvQZsjO+csVXKBA38VkzbX9ePjw2MaoPr/HKF/TRg+fuR55VsnRldXfEVG
        </contents>
    </archive>
</module>

```