/*
* A simple wrapperclass for uno-services.
* Copyright (C) 2005 Christoph Lutz
*
* This library is free software; you can redistribute it and/or
* modify it under the terms of the GNU Lesser General Public
* License as published by the Free Software Foundation; either
* version 2.1 of the License, or (at your option) any later version.
*
* This library is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
* Lesser General Public License for more details.
*
* You should have received a copy of the GNU Lesser General Public
* License along with this library; if not, write to the Free Software
* Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
*/

import java.util.Properties;
import java.util.Enumeration;
import java.io.FileInputStream;

import com.sun.star.comp.helper.Bootstrap;
import com.sun.star.beans.PropertyValue;
import com.sun.star.beans.XPropertySet;
import com.sun.star.frame.XComponentLoader;
import com.sun.star.frame.XDispatchProvider;
import com.sun.star.frame.XDispatchHelper;
import com.sun.star.lang.XMultiComponentFactory;
import com.sun.star.lang.XMultiServiceFactory;
import com.sun.star.lang.XComponent;
import com.sun.star.text.XTextDocument;
import com.sun.star.text.XTextRange;
import com.sun.star.uno.UnoRuntime;
import com.sun.star.uno.XComponentContext;
import com.sun.star.uno.XInterface;
import com.sun.star.bridge.XBridgeFactory;
import com.sun.star.bridge.XBridge;
import com.sun.star.connection.XConnector;
import com.sun.star.connection.XConnection;
import com.sun.star.util.XCloseable;
import com.sun.star.util.XSearchable;
import com.sun.star.util.XSearchDescriptor;
import com.sun.star.view.XSelectionSupplier;
import com.sun.star.text.XTextContent;
import com.sun.star.text.XTextCursor;
import com.sun.star.text.XText;
import com.sun.star.text.XDocumentIndex;
import com.sun.star.text.XDocumentIndexesSupplier;
import com.sun.star.container.XIndexAccess;
import com.sun.star.util.XRefreshable;


/**
* UnoService
*
*/
public class UnoService {
    
    private Object oDesktop;
    private XBridge xBridge = null ;
    private XComponentContext xRemoteContext = null;
    private XComponentLoader xComponentLoader = null;
    private XMultiComponentFactory xRemoteServiceManager = null;
    
    private String includeGD = null;
    private String insertindexGD = null;
    
    /**
    * This method connects to a running OpenOffice. This OpenOffice is
    * listening on a specific port. It connects to this port.
    *
    * @param oohost
    * @param ooport
    * @return
    */
    public UnoService(String oohost, String ooport)
    throws Exception
    {
        // Bootstrap initial context component
        XComponentContext _ctx =
        Bootstrap.createInitialComponentContext(null);
        xRemoteContext = _ctx ;
        
        // Get connection
        Object x = xRemoteContext.getServiceManager().createInstanceWithContext("com.sun.star.connection.Connector", xRemoteContext);
        XConnector xConnector = (XConnector) UnoRuntime.queryInterface(XConnector.class, x);
        XConnection connection = xConnector.connect("socket,host=" + oohost + ",port=" + ooport);
        if (connection == null) {
            System.out.println("Connection is null");
        }
        
        // Get bridge
        x = xRemoteContext.getServiceManager().createInstanceWithContext("com.sun.star.bridge.BridgeFactory", xRemoteContext);
        XBridgeFactory xBridgeFactory = (XBridgeFactory) UnoRuntime.queryInterface(XBridgeFactory.class, x);
        if (xBridgeFactory==null) {
            System.out.println("bridge factory is null");
        }
        // this is the bridge that you will dispose        
        xBridge = xBridgeFactory.createBridge("" , "urp", connection , null);
        // get the remote instance                         
        x = xBridge.getInstance("StarOffice.ServiceManager");
        
        // Query the initial object for its main factory interface
        xRemoteServiceManager = (XMultiComponentFactory)
        UnoRuntime.queryInterface(XMultiComponentFactory.class, x);
        // retrieve the component context (it's not yet exported from the office)
        // Query for the XPropertySet interface.
        XPropertySet xProperySet = (XPropertySet)
        UnoRuntime.queryInterface(XPropertySet.class, xRemoteServiceManager);
        
        // Get the default context from the office server.
        Object oDefaultContext =
        xProperySet.getPropertyValue("DefaultContext");
        
        // Query for the interface XComponentContext.
        XComponentContext xOfficeComponentContext = (XComponentContext) UnoRuntime.queryInterface(
            XComponentContext.class, oDefaultContext);
        // now create the desktop service
        // NOTE: use the office component context here !
        oDesktop = xRemoteServiceManager.createInstanceWithContext("com.sun.star.frame.Desktop", xOfficeComponentContext);
        
        xComponentLoader = (XComponentLoader)
        UnoRuntime.queryInterface(XComponentLoader.class, oDesktop);
        
        String available = (null !=xComponentLoader ? "available" : "not available");
        System.out.println("Remote ServiceManager is " + available);
    }
    
    /**
    * Release the bridge
    *
    */
    public void release() {
        XComponent xcomponent =
        (XComponent) UnoRuntime.queryInterface(XComponent.class, xBridge);
        // Closing the bridge
        xcomponent.dispose();
    }
    
    /**
    * A specified document is loaded. 
    * 
    * @param url
    * @return
    */
    public XComponent openDocument(String url)
    throws Exception
    {
        PropertyValue[] myProperties = new PropertyValue[1];
        myProperties[0] = new PropertyValue();
        myProperties[0].Name = "Hidden";
        // for open document and do not show user interface use "true"
        myProperties[0].Value = new Boolean(false);
        
        XComponent xComponent = null;
        
        // Load a given document
        xComponent = xComponentLoader.loadComponentFromURL(
            url,  // For createUNOFileURL see: ../Office/Office.CreateUNOCompatibleURL.snip
            "_blank",                       // New window
            0,                              // Is ignored
            myProperties);            // Special properties
        
        return xComponent;
    }
    
    /**
    * A specified document is closed and disposed.
    * 
    * @param xComponent
    * @return
    */
    public void closeDocument(XComponent xComponent) {
        // Get a reference to the document interface that can close a file
        XCloseable xCloseable = (XCloseable) UnoRuntime.queryInterface(XCloseable.class, xComponent);
        
        // Try to close it or explicitly dispose it
        // See http://doc.services.openoffice.org/wiki/Documentation/
        //          DevGuide/OfficeDev/Closing_Documents
        if (xCloseable != null) {
            try {
                xCloseable.close(false);
            } catch (com.sun.star.util.CloseVetoException ex) {
                XComponent xComp = (XComponent) UnoRuntime.queryInterface(XComponent.class, xComponent);
                xComp.dispose();
            }
        } else {
            XComponent xComp = (XComponent) UnoRuntime.queryInterface(XComponent.class, xComponent);
            xComp.dispose();
        }
    }
    
    /**
    * Execute a UNO command thru the dispatcher on a document .
    * 
    * @param xTextDocument
    * @param unoCommand
    * @return
    */
    public void dispatch(XTextDocument xTextDocument, String unoCommand, PropertyValue[] params)
    throws Exception
    {
        XMultiServiceFactory xFactory = (XMultiServiceFactory) UnoRuntime.queryInterface(XMultiServiceFactory.class, xRemoteServiceManager);
        Object oDispatchHelper = xFactory.createInstance("com.sun.star.frame.DispatchHelper");
        XDispatchHelper xDispatchHelper = (XDispatchHelper) UnoRuntime.queryInterface(XDispatchHelper.class, oDispatchHelper);
        XDispatchProvider xDispatchProvider = (XDispatchProvider) UnoRuntime.queryInterface(XDispatchProvider.class, xTextDocument.getCurrentController().getFrame());
        if (params==null) {
            params = new PropertyValue[0];
        }
        xDispatchHelper.executeDispatch(xDispatchProvider, unoCommand, "", 0, params);
    }
    
    public void includeDocs(XComponent xComponent, Properties data) throws Exception {
        XComponent xComponentInclude = null;
        try {
            // Open master document and get XTextDocument object
            XTextDocument xTextDocument = (XTextDocument)
            UnoRuntime.queryInterface(XTextDocument.class, xComponent);
            
            String prefix = "match.";
            Enumeration e = data.propertyNames();
            while (e.hasMoreElements()) {
                
                // Check for the doc properties
                String propName = (String) e.nextElement();
                if (!propName.startsWith(prefix)) {
                    continue;
                }
                
                // Get match information
                String docNum = propName.substring(prefix.length());
                String urlInclude = data.getProperty(propName);
                
                // Open slave document and get XTextDocument object
                xComponentInclude = this.openDocument(urlInclude);
                XTextDocument xTextDocumentInclude = (XTextDocument)
                UnoRuntime.queryInterface(XTextDocument.class, xComponentInclude);
                
                // Select all and copy
                this.dispatch(xTextDocumentInclude, ".uno:SelectAll", null);
                this.dispatch(xTextDocumentInclude, ".uno:Copy", null);
                
                // Search text in master doc
                XSearchable xSearchable = (XSearchable) UnoRuntime.queryInterface(XSearchable.class, xComponent);
                XSearchDescriptor xSearchDescr = xSearchable.createSearchDescriptor();
                xSearchDescr.setSearchString(this.includeGD+docNum+"}");
                XInterface xFound = (XInterface) xSearchable.findFirst(xSearchDescr);
                
                while (xFound!=null) {
                    
                    // Select text found in master doc
                    XTextRange xTextRange = (XTextRange) UnoRuntime.queryInterface(XTextRange.class, xFound);
                    XSelectionSupplier xSelectionSupplier = (XSelectionSupplier) UnoRuntime.queryInterface(XSelectionSupplier.class, xTextDocument.getCurrentController());
                    xSelectionSupplier.select(xTextRange);
                    
                    // Paste into master doc replacing text found
                    this.dispatch(xTextDocument, ".uno:Paste", null);
                    
                    // Search next
                    xFound = (XInterface) xSearchable.findNext(xTextRange.getEnd(), xSearchDescr);
                }
                
                // Close slave doc
                this.closeDocument(xComponentInclude);
            }
        }
        finally {
            // Make sure the document is closed
            if (xComponentInclude != null) {
                this.closeDocument(xComponentInclude);
            }
        }
    }
    
    public void insertIndexes(XComponent xComponent) throws Exception {
        
        // Get XTextDocument object
        XTextDocument xTextDocument = (XTextDocument)
        UnoRuntime.queryInterface(XTextDocument.class, xComponent);
        
        XMultiServiceFactory mxDocFactory = (XMultiServiceFactory) UnoRuntime.queryInterface(
            XMultiServiceFactory.class, xTextDocument);
        
        // Search text in master doc
        XSearchable xSearchable = (XSearchable) UnoRuntime.queryInterface(XSearchable.class, xComponent);
        XSearchDescriptor xSearchDescr = xSearchable.createSearchDescriptor();
        xSearchDescr.setSearchString(this.insertindexGD);
        XInterface xFound = (XInterface) xSearchable.findFirst(xSearchDescr);
        
        while (xFound!=null) {
            
            // Select text found in master doc
            XTextRange xTextRange = (XTextRange) UnoRuntime.queryInterface(XTextRange.class, xFound);
            
            // Remove placeholder
            xTextRange.setString("");
            
            // Create a ContentIndex and access it's XPropertySet interface
            XPropertySet xIndex = (XPropertySet) UnoRuntime.queryInterface(XPropertySet.class, mxDocFactory.createInstance ("com.sun.star.text.ContentIndex"));
            
            // Create index from outline
            xIndex.setPropertyValue ("CreateFromOutline", true);
            
            // Again, the Level property _must_ be set
            xIndex.setPropertyValue ("Level", new Short ((short) 10));
            
            // Access the XTextContent interfaces of both the Index and the
            // IndexMark
            XTextContent xIndexContent = (XTextContent) UnoRuntime.queryInterface(XTextContent.class, xIndex);
            //XTextContent xEntryContent = (XTextContent) UnoRuntime.queryInterface(XTextContent.class, xEntry);
            
            // get a reference to the body text of the document
            XText mxDocText = xTextDocument.getText();
            
            // Create a document cursor and remember it
            //XTextCursor mxDocCursor = mxDocText.createTextCursor();
            
            // Insert both in the document
            //mxDocText.insertTextContent (mxDocCursor, xEntryContent, false);
            mxDocText.insertTextContent (xTextRange, xIndexContent, false);
            
            // Get the XDocumentIndex interface of the Index
            XDocumentIndex xDocIndex = (XDocumentIndex) UnoRuntime.queryInterface(XDocumentIndex.class, xIndex);
            
            // And call its update method
            xDocIndex.update();
            
            // Search next
            xFound = (XInterface) xSearchable.findNext(xTextRange.getEnd(), xSearchDescr);
        }
    }
    
    public void updateIndexes(XComponent xComponent) throws Exception {
        
        // Get XTextDocument object
        XTextDocument xTextDocument = (XTextDocument)
        UnoRuntime.queryInterface(XTextDocument.class, xComponent);
        
        XDocumentIndexesSupplier xDocumentIndexesSupplier = (XDocumentIndexesSupplier) UnoRuntime.queryInterface(XDocumentIndexesSupplier.class, xTextDocument);
        XIndexAccess xIndexAccess = xDocumentIndexesSupplier.getDocumentIndexes();
        
        for(int i=0; i<xIndexAccess.getCount(); i++) {
            XDocumentIndex xDocIndex = (XDocumentIndex) UnoRuntime.queryInterface(XDocumentIndex.class, xIndexAccess.getByIndex(i));
            xDocIndex.update();
        }
    }
    
    /***************************************************************************
    * Command-line
    **************************************************************************/
    
    public static void main(String[] args)
    throws Exception
    {
        if (args.length<3) {
            System.out.println("UnoService: java UnoService ...");
            return;
        }
        
        String inputDataFilename = args[0];
        String sourceURL = args[1];
        String targetURL = args[2];
        
        FileInputStream inputDataFile = new FileInputStream(inputDataFilename);
        Properties data = new Properties();
        data.load(inputDataFile);
        inputDataFile.close();
        
        String propertiesFilename = "unoservice.properties";
        FileInputStream propertiesFile = new FileInputStream(propertiesFilename);
        Properties props = new Properties();
        props.load(propertiesFile);
        propertiesFile.close();
        
        // Create service object
        UnoService unoService = new UnoService(props.getProperty("service.host"), props.getProperty("service.port"));
        
        unoService.includeGD = data.getProperty("includeGD");
        unoService.insertindexGD = data.getProperty("insertindexGD");
        
        XComponent xComponent = null;
        
        try {
            // Open source document and save as target document
            xComponent = unoService.openDocument(sourceURL);
            XTextDocument xTextDocument = (XTextDocument)
            UnoRuntime.queryInterface(XTextDocument.class, xComponent);
            PropertyValue[] params = new PropertyValue[1];
            params[0] = new PropertyValue();
            params[0].Name = "URL";
            params[0].Value = targetURL;
            unoService.dispatch(xTextDocument, ".uno:SaveAs", params);
            
            // Operations
            XRefreshable xRefreshable = ((XRefreshable)UnoRuntime.queryInterface(XRefreshable.class, xTextDocument));
            //unoService.includeDocs(xComponent, data);
            //xRefreshable.refresh();
            unoService.insertIndexes(xComponent);
            xRefreshable.refresh();
            unoService.updateIndexes(xComponent);
            xRefreshable.refresh();
            
            // Save changes
            unoService.dispatch(xTextDocument, ".uno:Save", null);
            
            // Return OK on success
            System.out.println("OK");
        }
        finally {
            // Make sure the document is closed
            if (xComponent != null) {
                unoService.closeDocument(xComponent);
            }
            // Release all resources
            unoService.release();
        }
    }
    
}