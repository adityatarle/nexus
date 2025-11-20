<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ProductTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    /**
     * @return array
     */
    public function array(): array
    {
        // Return sample data row for reference
        return [
            [
                'Sample Product Name',
                'SAMPLE-SKU-001',
                'Tractors',
                'Heavy Duty Tractors',
                'This is a detailed description of the product',
                'Short description',
                1000.00,
                900.00,
                850.00,
                800.00,
                50,
                1,
                1,
                100.50,
                '100x50x30',
                'Brand Name',
                'Model XYZ',
                'Diesel',
                '1 Year',
                0,
                1,
                0,
                1,
                'Dealer notes here'
            ]
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Name *',
            'SKU *',
            'Category *',
            'Subcategory',
            'Description',
            'Short Description',
            'Price (INR) *',
            'Sale Price (INR)',
            'Dealer Price (INR)',
            'Dealer Sale Price (INR)',
            'Stock Quantity',
            'Manage Stock (1/0)',
            'In Stock (1/0)',
            'Weight',
            'Dimensions',
            'Brand',
            'Model',
            'Power Source',
            'Warranty',
            'Is Featured (1/0)',
            'Is Active (1/0)',
            'Is Dealer Exclusive (1/0)',
            'Dealer Min Quantity',
            'Dealer Notes'
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        // Style the header row
        $sheet->getStyle('A1:X1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Style the sample row
        $sheet->getStyle('A2:X2')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E7E6E6'],
            ],
        ]);

        // Add note row
        $sheet->setCellValue('A4', 'Instructions:');
        $sheet->setCellValue('A5', '1. Fields marked with * are required');
        $sheet->setCellValue('A6', '2. All prices should be in INR (Indian Rupees) - enter numeric values only');
        $sheet->setCellValue('A7', '3. If category does not exist, it will be created automatically');
        $sheet->setCellValue('A8', '4. If subcategory does not exist, it will be created automatically (must belong to the specified category)');
        $sheet->setCellValue('A9', '5. SKU must be unique');
        $sheet->setCellValue('A10', '6. For boolean fields (Manage Stock, In Stock, etc.), use 1 for Yes/True and 0 for No/False');
        $sheet->setCellValue('A11', '7. Delete the sample row (row 2) before adding your products');
        $sheet->setCellValue('A12', '8. Sale Price must be less than Price');
        $sheet->setCellValue('A13', '9. Dealer Sale Price must be less than Dealer Price');

        $sheet->getStyle('A4:A13')->applyFromArray([
            'font' => [
                'bold' => true,
                'italic' => true,
            ],
        ]);

        return [];
    }

    /**
     * @return array
     */
    public function columnWidths(): array
    {
        return [
            'A' => 25,  // Name
            'B' => 20,  // SKU
            'C' => 20,  // Category
            'D' => 20,  // Subcategory
            'E' => 40,  // Description
            'F' => 25,  // Short Description
            'G' => 15,  // Price
            'H' => 15,  // Sale Price
            'I' => 15,  // Dealer Price
            'J' => 18,  // Dealer Sale Price
            'K' => 15,  // Stock Quantity
            'L' => 18,  // Manage Stock
            'M' => 12,  // In Stock
            'N' => 12,  // Weight
            'O' => 15,  // Dimensions
            'P' => 15,  // Brand
            'Q' => 15,  // Model
            'R' => 15,  // Power Source
            'S' => 15,  // Warranty
            'T' => 15,  // Is Featured
            'U' => 12,  // Is Active
            'V' => 20,  // Is Dealer Exclusive
            'W' => 20,  // Dealer Min Quantity
            'X' => 30,  // Dealer Notes
        ];
    }
}

