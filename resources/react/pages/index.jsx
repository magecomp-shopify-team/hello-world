import React, { useState, useEffect } from "react";
import {
  Page,
  Card,
  DataTable,
  Button,
  InlineStack,
  BlockStack,
  ResourceList,
  Text,
  Icon,
  TextField,
} from "@shopify/polaris";
import { XSmallIcon } from "@shopify/polaris-icons";

export default function BundlesPage() {
  const [view, setView] = useState("list");
  const [bundles, setBundles] = useState([]);
  const [editingBundle, setEditingBundle] = useState(null);
  const [mainProduct, setMainProduct] = useState(null);
  const [bundleProducts, setBundleProducts] = useState([]);
  const [saving, setSaving] = useState(false);

  // Load bundles safely
  useEffect(() => {
    fetch("/api/bundles")
      .then((res) => res.json())
      .then((data) => {
        if (Array.isArray(data)) {
          setBundles(data);
        } else {
          console.error("Unexpected API response:", data);
          setBundles([]);
        }
      })
      .catch((err) => {
        console.error("Failed to load bundles:", err);
        setBundles([]);
      });
  }, []);

  // Create bundle
  const handleCreate = async () => {
    setEditingBundle(null);
    setBundleProducts([]);

    try {
      const picker = await shopify.resourcePicker({
        type: "product",
        multiple: false,
      });

      if (picker?.length > 0) {
        const selectedProduct = picker[0];
        setMainProduct({
          id: selectedProduct.id,
          title: selectedProduct.title,
          handle: selectedProduct.handle,
          image: selectedProduct?.images?.[0]?.originalSrc || null,
        });
        setView("builder");
      }
    } catch (err) {
      console.error("Error in product picker:", err);
    }
  };

  // Edit bundle
  const handleEdit = (bundle) => {
    setEditingBundle(bundle);
    setMainProduct(bundle.main_product_id);
    setBundleProducts(bundle.bundle_product_ids || []);
    setView("builder");
  };

  // Pick bundle products
  const handlePickBundleProducts = async () => {
    try {
      const savedIds = [mainProduct.id];
      const numericIds = savedIds.map((id) =>
        id.replace("gid://shopify/Product/", "")
      );
      const formattedIds = numericIds.map((id) => `-id:${id}`).join(" AND ");

      const picker = await shopify.resourcePicker({
        type: "product",
        multiple: true,
        filter: {
          query: formattedIds,
        },
        selectionIds: bundleProducts.map((p) => ({ id: p.id })),
      });

      if (picker?.length > 0) {
        const selectedProducts = picker.map((p) => ({
          id: p.id,
          title: p.title,
          handle: p.handle,
          image: p?.images?.[0]?.originalSrc || null,
        }));
        setBundleProducts(selectedProducts);
      }
    } catch (err) {
      console.error("Error in bundle picker:", err);
    }
  };

  const handleRemoveBundleItem = (id) => {
    setBundleProducts(bundleProducts.filter((p) => p.id !== id));
  };

  // Save bundle
  const handleSaveBundle = async () => {
    if (!mainProduct || bundleProducts.length === 0) {
      alert("Select a main product and at least one bundle product.");
      return;
    }

    setSaving(true);
    const payload = { mainProduct, bundleProducts };

    try {
      const res = await fetch("/api/bundle/save", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-Requested-With": "XMLHttpRequest",
        },
        body: JSON.stringify(payload),
      });

      const data = await res.json();

      if (data.success) {
        alert("Bundle saved successfully!");
        const savedBundle = {
          id: editingBundle?.id || data.bundle.id,
          main_product_id: mainProduct,
          main_product_title: mainProduct.title,
          bundle_product_ids: bundleProducts,
        };

        if (editingBundle) {
          setBundles((prev) =>
            prev.map((b) => (b.id === editingBundle.id ? savedBundle : b))
          );
        } else {
          setBundles((prev) => [...prev, savedBundle]);
        }

        // Reset and go back
        setView("list");
        setEditingBundle(null);
        setMainProduct(null);
        setBundleProducts([]);
      } else {
        console.error("Save error:", data);
        alert("Failed to save bundle. Check console for details.");
      }
    } catch (err) {
      console.error("Error saving bundle:", err);
      alert("Error saving bundle. Check console for details.");
    } finally {
      setSaving(false);
    }
  };

  // Delete bundle
  const handleDelete = async (bundleId) => {
    if (!confirm("Are you sure you want to delete this bundle?")) return;

    try {
      const res = await fetch(`/api/bundle/delete/${bundleId}`, {
        method: "DELETE",
        headers: {
          "Content-Type": "application/json",
          "X-Requested-With": "XMLHttpRequest",
        },
      });

      const data = await res.json();

      if (data.success) {
        alert("Bundle deleted successfully!");
        setBundles((prev) => prev.filter((b) => b.id !== bundleId));
      } else {
        alert("Failed to delete bundle.");
        console.error(data);
      }
    } catch (err) {
      alert("Error deleting bundle.");
      console.error(err);
    }
  };

  // DataTable rows
  const rows = (bundles || []).map((b) => [
    <div style={{ display: "flex", alignItems: "center", gap: "10px" }}>
      {b.main_product_id?.image && (
        <img
          src={b.main_product_id.image}
          alt={b.main_product_title}
          style={{
            width: "40px",
            height: "40px",
            objectFit: "cover",
            borderRadius: "4px",
          }}
        />
      )}
      <span>{b.main_product_title}</span>
    </div>,
    b.bundle_product_ids?.length || 0,
    <Button plain onClick={() => handleEdit(b)}>Edit</Button>,
    <Button destructive plain onClick={() => handleDelete(b.id)}>Delete</Button>,
  ]);

  return (
    <Page title="Product Bundles">
      {view === "list" && (
        <>
          <InlineStack gap="3" align="end" style={{ marginBottom: "20px" }}>
            <Button primary onClick={handleCreate}>
              Create Bundle
            </Button>
          </InlineStack>

          <Card sectioned>
            <DataTable
              columnContentTypes={["text", "numeric", "text", "text"]}
              headings={["Main Product", "Count", "Edit", "Delete"]}
              rows={rows}
            />
          </Card>
        </>
      )}

      {view === "builder" && (
        <Page title={editingBundle ? "Edit Bundle" : "Create Bundle"}>
          <InlineStack
            align="space-between"
            blockAlign="center"
            style={{ marginBottom: "20px" }}
          >
            <Button onClick={() => setView("list")}>Back to List</Button>
          </InlineStack>

          <Card sectioned>
            <BlockStack gap="5">
              <TextField
                label="Main Product"
                value={mainProduct?.title || ""}
                readOnly
              />

              <Card title="Bundle Products" sectioned>
                {bundleProducts.length === 0 ? (
                  <Text tone="subdued">No bundle products selected yet.</Text>
                ) : (
                  <ResourceList
                    resourceName={{ singular: "product", plural: "products" }}
                    items={bundleProducts}
                    renderItem={(item) => (
                      <ResourceList.Item id={item.id}>
                        <InlineStack align="space-between" blockAlign="center">
                          <Text fontWeight="bold">{item.title}</Text>
                          <Button
                            plain
                            onClick={() => handleRemoveBundleItem(item.id)}
                          >
                            <Icon source={XSmallIcon} color="base" />
                          </Button>
                        </InlineStack>
                      </ResourceList.Item>
                    )}
                  />
                )}
                <InlineStack gap="3" align="end" style={{ marginTop: "10px" }}>
                  <Button onClick={handlePickBundleProducts}>
                    Add Bundle Products
                  </Button>
                  {bundleProducts.length > 0 && (
                    <Button
                      primary
                      onClick={handleSaveBundle}
                      loading={saving}
                    >
                      Save Bundle
                    </Button>
                  )}
                </InlineStack>
              </Card>
            </BlockStack>
          </Card>
        </Page>
      )}
    </Page>
  );
}
