<?php
/**
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 19-4-15
 * Time: 下午3:09
 */

namespace gitter\output;


use gitter\core\DataItem;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExcelOutput extends Output
{
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    public function export()
    {
        $this->template = $this->template ?: __DIR__ . '/template/default.xlsx';

        $target = getenv('SAVE_DIR');
        $target = $target ? rtrim($target, '/') . '/' : dirname(dirname(__DIR__)) . '/';
        $target .= getenv('USERNAME') . '_' . date('W', strtotime(getenv('SINCE_DAY'))) . '周' . '_工作报告.xlsx';

        echo '正在导出中...' . PHP_EOL;

        $data = [];

        /** @var DataItem $item */
        foreach ($this->source as $item) {
            $date = date('w', strtotime($item->date));
            $date = $date == 0 ? 7 : $date;
            $data[$date][] = $item->message;
        }

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($this->template);
        $worksheet = $spreadsheet->getActiveSheet();

        // 填充数据
        foreach ($data as $date => $value) {
            $pos = $date * 2 + 3;

            $line = count($value);
            if ($line > 2) {
                $spreadsheet->getActiveSheet()->getRowDimension($pos)->setRowHeight(40 + ($line - 2) * 10);
            } elseif ($line == 2) {
                $spreadsheet->getActiveSheet()->getRowDimension($pos)->setRowHeight(30);
            }

            $value = implode(PHP_EOL, $value);
            $worksheet->getCell('B' . $pos)->setValue($value);
            $worksheet->getCell('H'. $pos)->setValue('已完成');
        }

        // 设置头部字体
        $this->setHeaderFont($spreadsheet);

        // 设置头部单元格背景色
        $this->setHeaderBg($worksheet);

        // 设置单元格边框颜色
        $this->setCellBorder($worksheet);

        // 设置指定单元格背景色
        $this->setCellBg($worksheet, ['A4', 'B4', 'H4', 'M4', 'A2', 'A19', 'B19', 'A22', 'A29']);

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($target);

        echo '导出完毕,文件保存在' . $target . PHP_EOL;

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
    }

    /**
     * @param Spreadsheet $spreadsheet
     */
    protected function setHeaderFont($spreadsheet)
    {
        // set A1
        $richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
        $richText->createText('工作周报（'.getenv('USERNAME').'）      ');
        $payable = $richText->createTextRun('日期：' . date('Y年m月d日',
                strtotime(getenv('SINCE_DAY'))) . ' --- ' . date('Y年m月d日', strtotime(getenv('UNTIL_DAY'))) . '   第'
            . (date('W', strtotime(getenv('SINCE_DAY')))) . '周');

        $payable->getFont()->setBold(true);
        $payable->getFont()->setItalic(true);
        $payable->getFont()
            ->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE));
        $spreadsheet->getActiveSheet()->getCell('A1')->setValue($richText);

        // set A2
        $spreadsheet->getActiveSheet()->getCell('A2')->setValue('部门名称：研发部            填写人员：'.getenv('USERNAME').'                  审核人员：');
    }

    /**
     * @param Worksheet $worksheet
     */
    protected function setCellBorder($worksheet)
    {
        $styleArray = [
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color'       => ['argb' => '9BBB59'],
                ],
            ],
        ];

        for ($i = 4; $i <= 30; $i++) {
            for ($j = ord('A'); $j <= ord('O'); $j++) {
                $c = chr($j) . $i;
                $worksheet->getStyle($c)->applyFromArray($styleArray);
            }
        }
    }

    /**
     * @param Worksheet $worksheet
     * @param           $cells
     */
    protected function setCellBg($worksheet, $cells)
    {
        foreach ($cells as $cell) {
            $worksheet->getCell($cell)->getStyle()->getFill()->applyFromArray([
                'fillType' => Fill::FILL_GRADIENT_PATH,
                'rotation' => 0,
                'color'    => [
                    'rgb' => 'D5E2BA'
                ],
            ]);
        }
    }

    /**
     * @param Worksheet $worksheet
     */
    protected function setHeaderBg($worksheet)
    {
        $worksheet->getCell('A1')->getStyle()->getFill()->applyFromArray([
            'fillType' => Fill::FILL_GRADIENT_PATH,
            'rotation' => 0,
            'color'    => [
                'rgb' => '9BBB59'
            ],
        ]);
    }
}