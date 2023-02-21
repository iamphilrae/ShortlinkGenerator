<?php
namespace ShortlinkGenerator;

use Carbon\Carbon;
use Exception;
use League\Csv\Reader;
use League\Csv\Statement;

class Bootstrap
{
    public string $ERR_RESPONSE = "";

    private string $dataDirectory = __DIR__ . "/../data/";


    /**
     * Entry point to the script.
     * @throws Exception
     */
    public function run(): string|false
    {
        $filepath = $this->latestDataFilepath();
        if(empty($filepath)) {
            $this->ERR_RESPONSE = "No data file found";
            return false;
        }

        $records = $this->parseCsvFile($this->latestDataFilepath());

        if(empty($records)) {
            $this->ERR_RESPONSE = "No records found in: " . $this->latestDataFilepath();
            return false;
        }

        foreach($records as $k=>$v)
        {
            $now = Carbon::now();
            $ref_code =
                str_pad(
                    strtolower(substr($v['ref'], 0, (str_contains($v['ref'], " ") ? strpos($v['ref'], " ") : null))),
                    4,
                    "0",
                    STR_PAD_LEFT
                );

            $shortlink = new ShortlinkModel();
            $shortlink->created_at      = $now->format('Y-m-d H:i:s');
            $shortlink->updated_at      = $now->format('Y-m-d H:i:s');
            $shortlink->scan_code       = sprintf("cw%s", $ref_code);
            $shortlink->real_url        = $v['url'];
            $shortlink->description_en  = $v['ref'];
            $shortlink->project         = $v['project'];

            $records[$k] = $shortlink;
        }


        $collection_insert_sql = "";

        foreach($records as $r)
            $collection_insert_sql .= $r->insertSql() . "\n\n";

        return $collection_insert_sql;
    }



    /**
     * @param bool $filenameOnly
     * @return string|null
     */
    private function latestDataFilepath(bool $filenameOnly=false): ?string
    {
        $files = scandir($this->dataDirectory);

        foreach($files as $k=>$v)
            if(strpos($v, '.') === 0)
                unset($files[$k]);

        if(empty($files))
            return null;

        return ($filenameOnly ? '' : $this->dataDirectory) . array_pop($files);
    }

    /**
     * @param string $filepath
     * @return ShortlinkModel[]|null
     * @throws Exception
     */
    private function parseCsvFile(string $filepath): ?array
    {
        $csv = Reader::createFromPath($filepath, 'r');
        $csv->setHeaderOffset(0);

        $records = Statement::create()->process($csv);
        $records = $csv->getRecords();

        $response = [];
        foreach ($records as $record) {
            $response[] = $record;
        }

        return empty($response) ? null : $response;
    }




}