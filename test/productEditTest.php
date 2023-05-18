<?php
use PHPUnit\Framework\TestCase;

class ProductEditTest extends TestCase
{
    public function testProductEdit()
    {
        // Simulates data for the test
        $_SERVER["REQUEST_METHOD"] = "POST";
        $_SESSION["email"] = "test@test.com";
        $_SESSION["roleId"] = 2;
        $_POST["productId"] = 1;
        $_POST["product"] = "New Product";
        $_POST["description"] = "Test description";
        $_POST["releasedate"] = "2013-09-17";
        $_POST["stock"] = 4;
        $_POST["price"] = 60;
        $_POST["productType"] = 1;
        

        // Captures output
        ob_start();
        require '../products/productEdit.php';
        $output = ob_get_clean();

        // Verify result
        $this->assertStringContainsString('Product updated successfully', $output);
    }
}