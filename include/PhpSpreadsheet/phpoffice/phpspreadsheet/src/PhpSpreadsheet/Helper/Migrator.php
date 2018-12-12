<?php

namespace PhpOffice\PhpSpreadsheet\Helper;

class Migrator
{
    /**
     * @var string[]
     */
    private $from;

    /**
     * @var string[]
     */
    private $to;

    public function __construct()
    {
        $this->from = array_keys($this->getMapping());
        $this->to = array_values($this->getMapping());
    }

    /**
     * Return the ordered mapping from old \PhpOffice\PhpSpreadsheet\Spreadsheet class names to new PhpSpreadsheet one.
     *
     * @return string[]
     */
    public function getMapping()
    {
        // Order matters here, we should have the deepest namespaces first (the most "unique" strings)
        $classes = [
            '\PhpOffice\PhpSpreadsheet\Shared\Escher\DggContainer\BstoreContainer\BSE\Blip' => \PhpOffice\PhpSpreadsheet\Shared\Escher\DggContainer\BstoreContainer\BSE\Blip::class,
            '\PhpOffice\PhpSpreadsheet\Shared\Escher\DgContainer\SpgrContainer\SpContainer' => \PhpOffice\PhpSpreadsheet\Shared\Escher\DgContainer\SpgrContainer\SpContainer::class,
            '\PhpOffice\PhpSpreadsheet\Shared\Escher\DggContainer\BstoreContainer\BSE' => \PhpOffice\PhpSpreadsheet\Shared\Escher\DggContainer\BstoreContainer\BSE::class,
            '\PhpOffice\PhpSpreadsheet\Shared\Escher\DgContainer\SpgrContainer' => \PhpOffice\PhpSpreadsheet\Shared\Escher\DgContainer\SpgrContainer::class,
            '\PhpOffice\PhpSpreadsheet\Shared\Escher\DggContainer\BstoreContainer' => \PhpOffice\PhpSpreadsheet\Shared\Escher\DggContainer\BstoreContainer::class,
            '\PhpOffice\PhpSpreadsheet\Shared\OLE\PPS\File' => \PhpOffice\PhpSpreadsheet\Shared\OLE\PPS\File::class,
            '\PhpOffice\PhpSpreadsheet\Shared\OLE\PPS\Root' => \PhpOffice\PhpSpreadsheet\Shared\OLE\PPS\Root::class,
            '\PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column\Rule' => \PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column\Rule::class,
            '\PhpOffice\PhpSpreadsheet\Writer\Ods\Cell\Comment' => \PhpOffice\PhpSpreadsheet\Writer\Ods\Cell\Comment::class,
            '\PhpOffice\PhpSpreadsheet\Calculation\Token\Stack' => \PhpOffice\PhpSpreadsheet\Calculation\Token\Stack::class,
            '\PhpOffice\PhpSpreadsheet\Chart\Renderer\JpGraph' => \PhpOffice\PhpSpreadsheet\Chart\Renderer\JpGraph::class,
            '\PhpOffice\PhpSpreadsheet\Reader\Xls\Escher' => \PhpOffice\PhpSpreadsheet\Reader\Xls\Escher::class,
            '\PhpOffice\PhpSpreadsheet\Reader\Xls\MD5' => \PhpOffice\PhpSpreadsheet\Reader\Xls\MD5::class,
            '\PhpOffice\PhpSpreadsheet\Reader\Xls\RC4' => \PhpOffice\PhpSpreadsheet\Reader\Xls\RC4::class,
            '\PhpOffice\PhpSpreadsheet\Reader\Xlsx\Chart' => \PhpOffice\PhpSpreadsheet\Reader\Xlsx\Chart::class,
            '\PhpOffice\PhpSpreadsheet\Reader\Xlsx\Theme' => \PhpOffice\PhpSpreadsheet\Reader\Xlsx\Theme::class,
            '\PhpOffice\PhpSpreadsheet\Shared\Escher\DgContainer' => \PhpOffice\PhpSpreadsheet\Shared\Escher\DgContainer::class,
            '\PhpOffice\PhpSpreadsheet\Shared\Escher\DggContainer' => \PhpOffice\PhpSpreadsheet\Shared\Escher\DggContainer::class,
            '\PhpOffice\PhpSpreadsheet\Shared\JAMA\CholeskyDecomposition' => \PhpOffice\PhpSpreadsheet\Shared\JAMA\PhpOffice\PhpSpreadsheet\Shared\JAMA\\PhpOffice\PhpSpreadsheet\Shared\JAMA\CholeskyDecomposition::class,
            '\PhpOffice\PhpSpreadsheet\Shared\JAMA\EigenvalueDecomposition' => \PhpOffice\PhpSpreadsheet\Shared\JAMA\PhpOffice\PhpSpreadsheet\Shared\JAMA\\PhpOffice\PhpSpreadsheet\Shared\JAMA\EigenvalueDecomposition::class,
            '\PhpOffice\PhpSpreadsheet\Shared\JAMA\LUDecomposition' => \PhpOffice\PhpSpreadsheet\Shared\JAMA\LUDecomposition::class,
            '\PhpOffice\PhpSpreadsheet\Shared\JAMA\Matrix' => \PhpOffice\PhpSpreadsheet\Shared\JAMA\Matrix::class,
            '\PhpOffice\PhpSpreadsheet\Shared\JAMA\QRDecomposition' => \PhpOffice\PhpSpreadsheet\Shared\JAMA\PhpOffice\PhpSpreadsheet\Shared\JAMA\\PhpOffice\PhpSpreadsheet\Shared\JAMA\QRDecomposition::class,
            'PHPExcel_Shared_JAMA_\PhpOffice\PhpSpreadsheet\Shared\JAMA\QRDecomposition' => \PhpOffice\PhpSpreadsheet\Shared\JAMA\PhpOffice\PhpSpreadsheet\Shared\JAMA\\PhpOffice\PhpSpreadsheet\Shared\JAMA\QRDecomposition::class,
            '\PhpOffice\PhpSpreadsheet\Shared\JAMA\SingularValueDecomposition' => \PhpOffice\PhpSpreadsheet\Shared\JAMA\PhpOffice\PhpSpreadsheet\Shared\JAMA\\PhpOffice\PhpSpreadsheet\Shared\JAMA\SingularValueDecomposition::class,
            '\PhpOffice\PhpSpreadsheet\Shared\OLE\ChainedBlockStream' => \PhpOffice\PhpSpreadsheet\Shared\OLE\ChainedBlockStream::class,
            '\PhpOffice\PhpSpreadsheet\Shared\OLE\PPS' => \PhpOffice\PhpSpreadsheet\Shared\OLE\PPS::class,
            '\PhpOffice\PhpSpreadsheet\Shared\Trend\BestFit' => \PhpOffice\PhpSpreadsheet\Shared\Trend\BestFit::class,
            '\PhpOffice\PhpSpreadsheet\Shared\Trend\ExponentialBestFit' => \PhpOffice\PhpSpreadsheet\Shared\Trend\ExponentialBestFit::class,
            '\PhpOffice\PhpSpreadsheet\Shared\Trend\LinearBestFit' => \PhpOffice\PhpSpreadsheet\Shared\Trend\LinearBestFit::class,
            '\PhpOffice\PhpSpreadsheet\Shared\Trend\LogarithmicBestFit' => \PhpOffice\PhpSpreadsheet\Shared\Trend\LogarithmicBestFit::class,
            '\PhpOffice\PhpSpreadsheet\Shared\Trend\PolynomialBestFit' => \PhpOffice\PhpSpreadsheet\Shared\Trend\PolynomialBestFit::class,
            '\PhpOffice\PhpSpreadsheet\Shared\Trend\PolynomialBestFit' => \PhpOffice\PhpSpreadsheet\Shared\Trend\PolynomialBestFit::class,
            '\PhpOffice\PhpSpreadsheet\Shared\Trend\PowerBestFit' => \PhpOffice\PhpSpreadsheet\Shared\Trend\PowerBestFit::class,
            '\PhpOffice\PhpSpreadsheet\Shared\Trend\PowerBestFit' => \PhpOffice\PhpSpreadsheet\Shared\Trend\PowerBestFit::class,
            '\PhpOffice\PhpSpreadsheet\Shared\Trend\Trend' => \PhpOffice\PhpSpreadsheet\Shared\Trend\Trend::class,
            '\PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column' => \PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column::class,
            '\PhpOffice\PhpSpreadsheet\Worksheet\Drawing\Shadow' => \PhpOffice\PhpSpreadsheet\Worksheet\Drawing\Shadow::class,
            '\PhpOffice\PhpSpreadsheet\Writer\Ods\Content' => \PhpOffice\PhpSpreadsheet\Writer\Ods\Content::class,
            '\PhpOffice\PhpSpreadsheet\Writer\Ods\Meta' => \PhpOffice\PhpSpreadsheet\Writer\Ods\Meta::class,
            '\PhpOffice\PhpSpreadsheet\Writer\Ods\MetaInf' => \PhpOffice\PhpSpreadsheet\Writer\Ods\MetaInf::class,
            '\PhpOffice\PhpSpreadsheet\Writer\Ods\Mimetype' => \PhpOffice\PhpSpreadsheet\Writer\Ods\Mimetype::class,
            '\PhpOffice\PhpSpreadsheet\Writer\Ods\Settings' => \PhpOffice\PhpSpreadsheet\Writer\Ods\Settings::class,
            '\PhpOffice\PhpSpreadsheet\Writer\Ods\Styles' => \PhpOffice\PhpSpreadsheet\Writer\Ods\Styles::class,
            '\PhpOffice\PhpSpreadsheet\Writer\Ods\Thumbnails' => \PhpOffice\PhpSpreadsheet\Writer\Ods\Thumbnails::class,
            '\PhpOffice\PhpSpreadsheet\Writer\Ods\WriterPart' => \PhpOffice\PhpSpreadsheet\Writer\Ods\WriterPart::class,
            '\PhpOffice\PhpSpreadsheet\Writer\Pdf' => \PhpOffice\PhpSpreadsheet\Writer\Pdf::class,
            '\PhpOffice\PhpSpreadsheet\Writer\Pdf\Dompdf' => \PhpOffice\PhpSpreadsheet\Writer\Pdf\Dompdf::class,
            '\PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf' => \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf::class,
            '\PhpOffice\PhpSpreadsheet\Writer\Pdf\Tcpdf' => \PhpOffice\PhpSpreadsheet\Writer\Pdf\Tcpdf::class,
            '\PhpOffice\PhpSpreadsheet\Writer\Xls\BIFFwriter' => \PhpOffice\PhpSpreadsheet\Writer\Xls\BIFFwriter::class,
            '\PhpOffice\PhpSpreadsheet\Writer\Xls\Escher' => \PhpOffice\PhpSpreadsheet\Writer\Xls\Escher::class,
            '\PhpOffice\PhpSpreadsheet\Writer\Xls\Font' => \PhpOffice\PhpSpreadsheet\Writer\Xls\Font::class,
            '\PhpOffice\PhpSpreadsheet\Writer\Xls\Parser' => \PhpOffice\PhpSpreadsheet\Writer\Xls\Parser::class,
            '\PhpOffice\PhpSpreadsheet\Writer\Xls\Workbook' => \PhpOffice\PhpSpreadsheet\Writer\Xls\Workbook::class,
            '\PhpOffice\PhpSpreadsheet\Writer\Xls\Worksheet' => \PhpOffice\PhpSpreadsheet\Writer\Xls\Worksheet::class,
            '\PhpOffice\PhpSpreadsheet\Writer\Xls\Xf' => \PhpOffice\PhpSpreadsheet\Writer\Xls\Xf::class,
            '\PhpOffice\PhpSpreadsheet\Writer\Xlsx\Chart' => \PhpOffice\PhpSpreadsheet\Writer\Xlsx\Chart::class,
            '\PhpOffice\PhpSpreadsheet\Writer\Xlsx\Comments' => \PhpOffice\PhpSpreadsheet\Writer\Xlsx\Comments::class,
            '\PhpOffice\PhpSpreadsheet\Writer\Xlsx\ContentTypes' => \PhpOffice\PhpSpreadsheet\Writer\Xlsx\ContentTypes::class,
            '\PhpOffice\PhpSpreadsheet\Writer\Xlsx\DocProps' => \PhpOffice\PhpSpreadsheet\Writer\Xlsx\DocProps::class,
            '\PhpOffice\PhpSpreadsheet\Writer\Xlsx\Drawing' => \PhpOffice\PhpSpreadsheet\Writer\Xlsx\Drawing::class,
            '\PhpOffice\PhpSpreadsheet\Writer\Xlsx\Rels' => \PhpOffice\PhpSpreadsheet\Writer\Xlsx\Rels::class,
            '\PhpOffice\PhpSpreadsheet\Writer\Xlsx\RelsRibbon' => \PhpOffice\PhpSpreadsheet\Writer\Xlsx\RelsRibbon::class,
            '\PhpOffice\PhpSpreadsheet\Writer\Xlsx\RelsVBA' => \PhpOffice\PhpSpreadsheet\Writer\Xlsx\RelsVBA::class,
            '\PhpOffice\PhpSpreadsheet\Writer\Xlsx\StringTable' => \PhpOffice\PhpSpreadsheet\Writer\Xlsx\StringTable::class,
            '\PhpOffice\PhpSpreadsheet\Writer\Xlsx\Style' => \PhpOffice\PhpSpreadsheet\Writer\Xlsx\Style::class,
            '\PhpOffice\PhpSpreadsheet\Writer\Xlsx\Theme' => \PhpOffice\PhpSpreadsheet\Writer\Xlsx\Theme::class,
            '\PhpOffice\PhpSpreadsheet\Writer\Xlsx\Workbook' => \PhpOffice\PhpSpreadsheet\Writer\Xlsx\Workbook::class,
            '\PhpOffice\PhpSpreadsheet\Writer\Xlsx\Worksheet' => \PhpOffice\PhpSpreadsheet\Writer\Xlsx\Worksheet::class,
            '\PhpOffice\PhpSpreadsheet\Writer\Xlsx\WriterPart' => \PhpOffice\PhpSpreadsheet\Writer\Xlsx\WriterPart::class,
            '\PhpOffice\PhpSpreadsheet\Collection\Cells' => \PhpOffice\PhpSpreadsheet\Collection\Cells::class,
            '\PhpOffice\PhpSpreadsheet\Calculation\Engine\CyclicReferenceStack' => \PhpOffice\PhpSpreadsheet\Calculation\Engine\CyclicReferenceStack::class,
            '\PhpOffice\PhpSpreadsheet\Calculation\Engine\Logger' => \PhpOffice\PhpSpreadsheet\Calculation\Engine\Logger::class,
            '\PhpOffice\PhpSpreadsheet\Calculation\Functions' => \PhpOffice\PhpSpreadsheet\Calculation\Functions::class,
            '\PhpOffice\PhpSpreadsheet\Calculation\Category' => \PhpOffice\PhpSpreadsheet\Calculation\Category::class,
            '\PhpOffice\PhpSpreadsheet\Calculation\Database' => \PhpOffice\PhpSpreadsheet\Calculation\Database::class,
            '\PhpOffice\PhpSpreadsheet\Calculation\DateTime' => \PhpOffice\PhpSpreadsheet\Calculation\DateTime::class,
            '\PhpOffice\PhpSpreadsheet\Calculation\Engineering' => \PhpOffice\PhpSpreadsheet\Calculation\Engineering::class,
            '\PhpOffice\PhpSpreadsheet\Calculation\Exception' => \PhpOffice\PhpSpreadsheet\Calculation\Exception::class,
            '\PhpOffice\PhpSpreadsheet\Calculation\ExceptionHandler' => \PhpOffice\PhpSpreadsheet\Calculation\ExceptionHandler::class,
            '\PhpOffice\PhpSpreadsheet\Calculation\Financial' => \PhpOffice\PhpSpreadsheet\Calculation\Financial::class,
            '\PhpOffice\PhpSpreadsheet\Calculation\FormulaParser' => \PhpOffice\PhpSpreadsheet\Calculation\FormulaParser::class,
            '\PhpOffice\PhpSpreadsheet\Calculation\FormulaToken' => \PhpOffice\PhpSpreadsheet\Calculation\FormulaToken::class,
            '\PhpOffice\PhpSpreadsheet\Calculation\Logical' => \PhpOffice\PhpSpreadsheet\Calculation\Logical::class,
            '\PhpOffice\PhpSpreadsheet\Calculation\LookupRef' => \PhpOffice\PhpSpreadsheet\Calculation\LookupRef::class,
            '\PhpOffice\PhpSpreadsheet\Calculation\MathTrig' => \PhpOffice\PhpSpreadsheet\Calculation\MathTrig::class,
            '\PhpOffice\PhpSpreadsheet\Calculation\Statistical' => \PhpOffice\PhpSpreadsheet\Calculation\Statistical::class,
            '\PhpOffice\PhpSpreadsheet\Calculation\TextData' => \PhpOffice\PhpSpreadsheet\Calculation\TextData::class,
            '\PhpOffice\PhpSpreadsheet\Cell\AdvancedValueBinder' => \PhpOffice\PhpSpreadsheet\Cell\AdvancedValueBinder::class,
            '\PhpOffice\PhpSpreadsheet\Cell\DataType' => \PhpOffice\PhpSpreadsheet\Cell\DataType::class,
            '\PhpOffice\PhpSpreadsheet\Cell\DataValidation' => \PhpOffice\PhpSpreadsheet\Cell\DataValidation::class,
            '\PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder' => \PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder::class,
            '\PhpOffice\PhpSpreadsheet\Cell\Hyperlink' => \PhpOffice\PhpSpreadsheet\Cell\Hyperlink::class,
            '\PhpOffice\PhpSpreadsheet\Cell\IValueBinder' => \PhpOffice\PhpSpreadsheet\Cell\IValueBinder::class,
            '\PhpOffice\PhpSpreadsheet\Chart\Axis' => \PhpOffice\PhpSpreadsheet\Chart\Axis::class,
            '\PhpOffice\PhpSpreadsheet\Chart\DataSeries' => \PhpOffice\PhpSpreadsheet\Chart\DataSeries::class,
            '\PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues' => \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues::class,
            '\PhpOffice\PhpSpreadsheet\Chart\Exception' => \PhpOffice\PhpSpreadsheet\Chart\Exception::class,
            '\PhpOffice\PhpSpreadsheet\Chart\GridLines' => \PhpOffice\PhpSpreadsheet\Chart\GridLines::class,
            '\PhpOffice\PhpSpreadsheet\Chart\Layout' => \PhpOffice\PhpSpreadsheet\Chart\Layout::class,
            '\PhpOffice\PhpSpreadsheet\Chart\Legend' => \PhpOffice\PhpSpreadsheet\Chart\Legend::class,
            '\PhpOffice\PhpSpreadsheet\Chart\PlotArea' => \PhpOffice\PhpSpreadsheet\Chart\PlotArea::class,
            '\PhpOffice\PhpSpreadsheet\Chart\Properties' => \PhpOffice\PhpSpreadsheet\Chart\Properties::class,
            '\PhpOffice\PhpSpreadsheet\Chart\Title' => \PhpOffice\PhpSpreadsheet\Chart\Title::class,
            '\PhpOffice\PhpSpreadsheet\Document\Properties' => \PhpOffice\PhpSpreadsheet\Document\Properties::class,
            '\PhpOffice\PhpSpreadsheet\Document\Security' => \PhpOffice\PhpSpreadsheet\Document\Security::class,
            '\PhpOffice\PhpSpreadsheet\Helper\Html' => \PhpOffice\PhpSpreadsheet\Helper\Html::class,
            '\PhpOffice\PhpSpreadsheet\Reader\BaseReader' => \PhpOffice\PhpSpreadsheet\Reader\BaseReader::class,
            '\PhpOffice\PhpSpreadsheet\Reader\Csv' => \PhpOffice\PhpSpreadsheet\Reader\Csv::class,
            '\PhpOffice\PhpSpreadsheet\Reader\DefaultReadFilter' => \PhpOffice\PhpSpreadsheet\Reader\DefaultReadFilter::class,
            '\PhpOffice\PhpSpreadsheet\Reader\Xml' => \PhpOffice\PhpSpreadsheet\Reader\Xml::class,
            '\PhpOffice\PhpSpreadsheet\Reader\Exception' => \PhpOffice\PhpSpreadsheet\Reader\Exception::class,
            '\PhpOffice\PhpSpreadsheet\Reader\Gnumeric' => \PhpOffice\PhpSpreadsheet\Reader\Gnumeric::class,
            '\PhpOffice\PhpSpreadsheet\Reader\Html' => \PhpOffice\PhpSpreadsheet\Reader\Html::class,
            '\PhpOffice\PhpSpreadsheet\Reader\IReadFilter' => \PhpOffice\PhpSpreadsheet\Reader\IReadFilter::class,
            '\PhpOffice\PhpSpreadsheet\Reader\IReader' => \PhpOffice\PhpSpreadsheet\Reader\IReader::class,
            '\PhpOffice\PhpSpreadsheet\Reader\Ods' => \PhpOffice\PhpSpreadsheet\Reader\Ods::class,
            '\PhpOffice\PhpSpreadsheet\Reader\Slk' => \PhpOffice\PhpSpreadsheet\Reader\Slk::class,
            '\PhpOffice\PhpSpreadsheet\Reader\Xls' => \PhpOffice\PhpSpreadsheet\Reader\Xls::class,
            '\PhpOffice\PhpSpreadsheet\Reader\Xlsx' => \PhpOffice\PhpSpreadsheet\Reader\Xlsx::class,
            '\PhpOffice\PhpSpreadsheet\RichText\ITextElement' => \PhpOffice\PhpSpreadsheet\RichText\ITextElement::class,
            '\PhpOffice\PhpSpreadsheet\RichText\Run' => \PhpOffice\PhpSpreadsheet\RichText\Run::class,
            '\PhpOffice\PhpSpreadsheet\RichText\TextElement' => \PhpOffice\PhpSpreadsheet\RichText\TextElement::class,
            '\PhpOffice\PhpSpreadsheet\Shared\CodePage' => \PhpOffice\PhpSpreadsheet\Shared\CodePage::class,
            '\PhpOffice\PhpSpreadsheet\Shared\Date' => \PhpOffice\PhpSpreadsheet\Shared\Date::class,
            '\PhpOffice\PhpSpreadsheet\Shared\Drawing' => \PhpOffice\PhpSpreadsheet\Shared\Drawing::class,
            '\PhpOffice\PhpSpreadsheet\Shared\Escher' => \PhpOffice\PhpSpreadsheet\Shared\Escher::class,
            '\PhpOffice\PhpSpreadsheet\Shared\File' => \PhpOffice\PhpSpreadsheet\Shared\File::class,
            '\PhpOffice\PhpSpreadsheet\Shared\Font' => \PhpOffice\PhpSpreadsheet\Shared\Font::class,
            '\PhpOffice\PhpSpreadsheet\Shared\OLE' => \PhpOffice\PhpSpreadsheet\Shared\OLE::class,
            '\PhpOffice\PhpSpreadsheet\Shared\OLERead' => \PhpOffice\PhpSpreadsheet\Shared\OLERead::class,
            '\PhpOffice\PhpSpreadsheet\Shared\PasswordHasher' => \PhpOffice\PhpSpreadsheet\Shared\PasswordHasher::class,
            '\PhpOffice\PhpSpreadsheet\Shared\StringHelper' => \PhpOffice\PhpSpreadsheet\Shared\StringHelper::class,
            '\PhpOffice\PhpSpreadsheet\Shared\TimeZone' => \PhpOffice\PhpSpreadsheet\Shared\TimeZone::class,
            '\PhpOffice\PhpSpreadsheet\Shared\XMLWriter' => \PhpOffice\PhpSpreadsheet\Shared\XMLWriter::class,
            '\PhpOffice\PhpSpreadsheet\Shared\Xls' => \PhpOffice\PhpSpreadsheet\Shared\Xls::class,
            '\PhpOffice\PhpSpreadsheet\Style\Alignment' => \PhpOffice\PhpSpreadsheet\Style\Alignment::class,
            '\PhpOffice\PhpSpreadsheet\Style\Border' => \PhpOffice\PhpSpreadsheet\Style\Border::class,
            '\PhpOffice\PhpSpreadsheet\Style\Borders' => \PhpOffice\PhpSpreadsheet\Style\Borders::class,
            '\PhpOffice\PhpSpreadsheet\Style\Color' => \PhpOffice\PhpSpreadsheet\Style\Color::class,
            '\PhpOffice\PhpSpreadsheet\Style\Conditional' => \PhpOffice\PhpSpreadsheet\Style\Conditional::class,
            '\PhpOffice\PhpSpreadsheet\Style\Fill' => \PhpOffice\PhpSpreadsheet\Style\Fill::class,
            '\PhpOffice\PhpSpreadsheet\Style\Font' => \PhpOffice\PhpSpreadsheet\Style\Font::class,
            '\PhpOffice\PhpSpreadsheet\Style\NumberFormat' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::class,
            '\PhpOffice\PhpSpreadsheet\Style\Protection' => \PhpOffice\PhpSpreadsheet\Style\Protection::class,
            '\PhpOffice\PhpSpreadsheet\Style\Supervisor' => \PhpOffice\PhpSpreadsheet\Style\Supervisor::class,
            '\PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter' => \PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter::class,
            '\PhpOffice\PhpSpreadsheet\Worksheet\BaseDrawing' => \PhpOffice\PhpSpreadsheet\Worksheet\BaseDrawing::class,
            '\PhpOffice\PhpSpreadsheet\Worksheet\CellIterator' => \PhpOffice\PhpSpreadsheet\Worksheet\CellIterator::class,
            '\PhpOffice\PhpSpreadsheet\Worksheet\Column' => \PhpOffice\PhpSpreadsheet\Worksheet\Column::class,
            '\PhpOffice\PhpSpreadsheet\Worksheet\ColumnCellIterator' => \PhpOffice\PhpSpreadsheet\Worksheet\ColumnCellIterator::class,
            '\PhpOffice\PhpSpreadsheet\Worksheet\ColumnDimension' => \PhpOffice\PhpSpreadsheet\Worksheet\ColumnDimension::class,
            '\PhpOffice\PhpSpreadsheet\Worksheet\ColumnIterator' => \PhpOffice\PhpSpreadsheet\Worksheet\ColumnIterator::class,
            '\PhpOffice\PhpSpreadsheet\Worksheet\Drawing' => \PhpOffice\PhpSpreadsheet\Worksheet\Drawing::class,
            '\PhpOffice\PhpSpreadsheet\Worksheet\HeaderFooter' => \PhpOffice\PhpSpreadsheet\Worksheet\HeaderFooter::class,
            '\PhpOffice\PhpSpreadsheet\Worksheet\HeaderFooterDrawing' => \PhpOffice\PhpSpreadsheet\Worksheet\HeaderFooterDrawing::class,
            '\PhpOffice\PhpSpreadsheet\Worksheet\Iterator' => \PhpOffice\PhpSpreadsheet\Worksheet\Iterator::class,
            '\PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing' => \PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing::class,
            '\PhpOffice\PhpSpreadsheet\Worksheet\PageMargins' => \PhpOffice\PhpSpreadsheet\Worksheet\PageMargins::class,
            '\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup' => \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::class,
            '\PhpOffice\PhpSpreadsheet\Worksheet\Protection' => \PhpOffice\PhpSpreadsheet\Worksheet\Protection::class,
            '\PhpOffice\PhpSpreadsheet\Worksheet\Row' => \PhpOffice\PhpSpreadsheet\Worksheet\Row::class,
            '\PhpOffice\PhpSpreadsheet\Worksheet\RowCellIterator' => \PhpOffice\PhpSpreadsheet\Worksheet\RowCellIterator::class,
            '\PhpOffice\PhpSpreadsheet\Worksheet\RowDimension' => \PhpOffice\PhpSpreadsheet\Worksheet\RowDimension::class,
            '\PhpOffice\PhpSpreadsheet\Worksheet\RowIterator' => \PhpOffice\PhpSpreadsheet\Worksheet\RowIterator::class,
            '\PhpOffice\PhpSpreadsheet\Worksheet\SheetView' => \PhpOffice\PhpSpreadsheet\Worksheet\SheetView::class,
            '\PhpOffice\PhpSpreadsheet\Writer\BaseWriter' => \PhpOffice\PhpSpreadsheet\Writer\BaseWriter::class,
            '\PhpOffice\PhpSpreadsheet\Writer\Csv' => \PhpOffice\PhpSpreadsheet\Writer\Csv::class,
            '\PhpOffice\PhpSpreadsheet\Writer\Exception' => \PhpOffice\PhpSpreadsheet\Writer\Exception::class,
            '\PhpOffice\PhpSpreadsheet\Writer\Html' => \PhpOffice\PhpSpreadsheet\Writer\Html::class,
            '\PhpOffice\PhpSpreadsheet\Writer\IWriter' => \PhpOffice\PhpSpreadsheet\Writer\IWriter::class,
            '\PhpOffice\PhpSpreadsheet\Writer\Ods' => \PhpOffice\PhpSpreadsheet\Writer\Ods::class,
            '\PhpOffice\PhpSpreadsheet\Writer\Pdf' => \PhpOffice\PhpSpreadsheet\Writer\Pdf::class,
            '\PhpOffice\PhpSpreadsheet\Writer\Xls' => \PhpOffice\PhpSpreadsheet\Writer\Xls::class,
            '\PhpOffice\PhpSpreadsheet\Writer\Xlsx' => \PhpOffice\PhpSpreadsheet\Writer\Xlsx::class,
            '\PhpOffice\PhpSpreadsheet\Collection\CellsFactory' => \PhpOffice\PhpSpreadsheet\Collection\CellsFactory::class,
            '\PhpOffice\PhpSpreadsheet\Calculation\Calculation' => \PhpOffice\PhpSpreadsheet\Calculation\Calculation::class,
            '\PhpOffice\PhpSpreadsheet\Cell\Cell' => \PhpOffice\PhpSpreadsheet\Cell\Cell::class,
            '\PhpOffice\PhpSpreadsheet\Chart\Chart' => \PhpOffice\PhpSpreadsheet\Chart\Chart::class,
            '\PhpOffice\PhpSpreadsheet\Comment' => \PhpOffice\PhpSpreadsheet\Comment::class,
            '\PhpOffice\PhpSpreadsheet\Exception' => \PhpOffice\PhpSpreadsheet\Exception::class,
            '\PhpOffice\PhpSpreadsheet\HashTable' => \PhpOffice\PhpSpreadsheet\HashTable::class,
            '\PhpOffice\PhpSpreadsheet\IComparable' => \PhpOffice\PhpSpreadsheet\IComparable::class,
            '\PhpOffice\PhpSpreadsheet\IOFactory' => \PhpOffice\PhpSpreadsheet\IOFactory::class,
            '\PhpOffice\PhpSpreadsheet\NamedRange' => \PhpOffice\PhpSpreadsheet\NamedRange::class,
            '\PhpOffice\PhpSpreadsheet\ReferenceHelper' => \PhpOffice\PhpSpreadsheet\ReferenceHelper::class,
            '\PhpOffice\PhpSpreadsheet\RichText\RichText' => \PhpOffice\PhpSpreadsheet\RichText\RichText::class,
            '\PhpOffice\PhpSpreadsheet\Settings' => \PhpOffice\PhpSpreadsheet\Settings::class,
            '\PhpOffice\PhpSpreadsheet\Style\Style' => \PhpOffice\PhpSpreadsheet\Style\Style::class,
            '\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet' => \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::class,
        ];

        $methods = [
            'MINUTE' => 'MINUTE',
            'SECOND' => 'SECOND',
            'WEEKDAY' => 'WEEKDAY',
            'WEEKNUM' => 'WEEKNUM',
            'excelToDateTimeObject' => 'excelToDateTimeObject',
            'excelToTimestamp' => 'excelToTimestamp',
            'formattedPHPToExcel' => 'formattedPHPToExcel',
            'Coordinate::absoluteCoordinate' => 'Coordinate::absoluteCoordinate',
            'Coordinate::absoluteReference' => 'Coordinate::absoluteReference',
            'Coordinate::buildRange' => 'Coordinate::buildRange',
            'Coordinate::columnIndexFromString' => 'Coordinate::columnIndexFromString',
            'Coordinate::coordinateFromString' => 'Coordinate::coordinateFromString',
            'Coordinate::extractAllCellReferencesInRange' => 'Coordinate::extractAllCellReferencesInRange',
            'Coordinate::getRangeBoundaries' => 'Coordinate::getRangeBoundaries',
            'Coordinate::mergeRangesInCollection' => 'Coordinate::mergeRangesInCollection',
            'Coordinate::rangeBoundaries' => 'Coordinate::rangeBoundaries',
            'Coordinate::rangeDimension' => 'Coordinate::rangeDimension',
            'Coordinate::splitRange' => 'Coordinate::splitRange',
            'Coordinate::stringFromColumnIndex' => 'Coordinate::stringFromColumnIndex',
        ];

        // Keep '\' prefix for class names
        $prefixedClasses = [];
        foreach ($classes as $key => &$value) {
            $value = str_replace('PhpOffice\\', '\\PhpOffice\\', $value);
            $prefixedClasses['\\' . $key] = $value;
        }
        $mapping = $prefixedClasses + $classes + $methods;

        return $mapping;
    }

    /**
     * Search in all files in given directory.
     *
     * @param string $path
     */
    private function recursiveReplace($path)
    {
        $patterns = [
            '/*.md',
            '/*.txt',
            '/*.TXT',
            '/*.php',
            '/*.phpt',
            '/*.php3',
            '/*.php4',
            '/*.php5',
            '/*.phtml',
        ];

        foreach ($patterns as $pattern) {
            foreach (glob($path . $pattern) as $file) {
                if (strpos($path, '/vendor/') !== false) {
                    echo $file . " skipped\n";

                    continue;
                }
                $original = file_get_contents($file);
                $converted = $this->replace($original);

                if ($original !== $converted) {
                    echo $file . " converted\n";
                    file_put_contents($file, $converted);
                }
            }
        }

        // Do the recursion in subdirectory
        foreach (glob($path . '/*', GLOB_ONLYDIR) as $subpath) {
            if (strpos($subpath, $path . '/') === 0) {
                $this->recursiveReplace($subpath);
            }
        }
    }

    public function migrate()
    {
        $path = realpath(getcwd());
        echo 'This will search and replace recursively in ' . $path . PHP_EOL;
        echo 'You MUST backup your files first, or you risk losing data.' . PHP_EOL;
        echo 'Are you sure ? (y/n)';

        $confirm = fread(STDIN, 1);
        if ($confirm === 'y') {
            $this->recursiveReplace($path);
        }
    }

    /**
     * Migrate the given code from \PhpOffice\PhpSpreadsheet\Spreadsheet to PhpSpreadsheet.
     *
     * @param string $original
     *
     * @return string
     */
    public function replace($original)
    {
        $converted = str_replace($this->from, $this->to, $original);

        // The string "\PhpOffice\PhpSpreadsheet\Spreadsheet" gets special treatment because of how common it might be.
        // This regex requires a word boundary around the string, and it can't be
        // preceded by $ or -> (goal is to filter out cases where a variable is named $PHPExcel or similar)
        $converted = preg_replace('~(?<!\$|->)(\b|\\\\)\PhpOffice\PhpSpreadsheet\Spreadsheet\b~', '\\' . \PhpOffice\PhpSpreadsheet\Spreadsheet::class, $converted);

        return $converted;
    }
}
