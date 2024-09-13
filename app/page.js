'use client'
import { useState, useEffect } from "react";
import axios from "axios";
import Loader from "./Loader";
import { host } from "./host";

export default function Home() {
  const [fontFile, setFontFile] = useState(null);
  const [fonts, setFonts] = useState([]);
  const [errorMessage, setErrorMessage] = useState("");
  const [uploadMessage, setUploadMessage] = useState("");
  const [isLoading, setIsLoading] = useState(false)

  // Function to handle file upload using Axios
  const uploadFile = (formData) => {
    console.log("File is uploading");
    
    setIsLoading(true)
    axios.post(`${host}/uploadFont.php`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    })
      .then(response => {
        console.log("Line25",response);
        setIsLoading(false)
        if (response.data.error) {
          setUploadMessage(response.data.error);
        } else {
          setUploadMessage(response.data.message);
          fetchFonts();
        }
      })
      .catch((error) => {
        setIsLoading(false)
        setUploadMessage("Error uploading file");
        console.error('Error:', error);
      });
  };

  // Fetch fonts from the server
  const fetchFonts = () => {
    setIsLoading(true)
    axios.get(`${host}/fontFile.php`)
      .then(response => {
        console.log("Font line 46",response);
        
        setIsLoading(false)
        setFonts(response.data.fonts || []);
      })
      .catch((error) => {
        setIsLoading(false)
        console.log(error);
        setErrorMessage("Error fetching fonts");
      });
  };

  useEffect(() => {
    axios.get(`${host}/DB/db.php`)
      .then((response) => {
        console.log('Response from PHP:', response.data);
      })
      .catch((error) => {
        console.error('Error fetching PHP API:', error);
      });
    fetchFonts();
  }, []);

  // Create a style element to manage all font-face rules
  useEffect(() => {
    const style = document.createElement('style');
    document.head.appendChild(style);

    // Function to update the font-face rules
    const updateFontFaces = () => {
      let fontFaces = '';

      fonts.forEach(font => {
        const name = font.name.split('.')[0]; // Use the name part before the extension
        const fontPath = `../fonts/${font.name}`; // Ensure the path is correct
        fontFaces += `
            @font-face {
              font-family: '${name}';
              src: url('${fontPath}');
            }
          `;
      });

      style.innerHTML = fontFaces;
    };

    updateFontFaces();

    // Clean up function to remove the style element
    return () => {
      document.head.removeChild(style);
    };
  }, [fonts]);

  // Handle file drop
  const handleFileDrop = (e) => {
    e.preventDefault();
    const file = e.dataTransfer.files[0];
    const extension = file.name.split('.').pop();

    if (file && extension === "ttf") {
      const formData = new FormData();
      formData.append('fontFile', file);
      setFontFile(file);
      setErrorMessage("");
      uploadFile(formData);
    } else {
      setFontFile(null);
      setErrorMessage("Only .ttf files are allowed!");
    }
  };

  // Handle Delete Font
  const handleDeleteFont = (id) => {
    axios.post(`${host}/delete_font.php`, JSON.stringify({ id }), {
      headers: {
        'Content-Type': 'application/json'
      }
    })
      .then(response => {
        console.log('Response:', response.data);
        if (response.data) {
          fetchFonts(); // Refresh the font list after deletion
        } else {
          setErrorMessage(response.data.message);
        }
      })
      .catch(error => {
        setIsLoading(false)
        console.error('Error deleting font:', error);
        setErrorMessage("Error deleting font");
      });
  };


  // Handle drag over event
  const handleDragOver = (e) => {
    e.preventDefault();
  };
  return (<>
    {
      isLoading ? <Loader /> :
        <div className="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
          <div
            className="flex flex-col items-center justify-center h-40 w-full sm:w-3/4 lg:w-1/2 border-2 border-dashed border-gray-400 rounded-lg text-gray-500 cursor-pointer mx-auto"
            onDrop={handleFileDrop}
            onDragOver={handleDragOver}
          >
            {fontFile ? (
              <p className="text-green-500 text-sm sm:text-lg">Font file: {fontFile.name}</p>
            ) : (
              <p className="text-sm sm:text-lg">Drag & Drop .ttf file here</p>
            )}
            {errorMessage && <p className="text-red-500 text-sm sm:text-md">{errorMessage}</p>}
          </div>
          {uploadMessage && <p className="mt-4 text-center text-sm sm:text-md">{uploadMessage}</p>}

          <div className="overflow-x-auto my-5">
            <b className="text-lg sm:text-xl">Our Fonts</b>
            <p className="text-xs sm:text-sm text-gray-400 my-2">
              Browse a list of Zepto fonts to build your font group.
            </p>
            <table className="min-w-full bg-white border my-2 border-gray-200 rounded-lg shadow-md">
              <thead className="bg-gray-100 text-gray-600 uppercase text-xs sm:text-sm">
                <tr className="text-left">
                  <th className="py-3 px-4 border-b">Font Name</th>
                  <th className="py-3 px-4 border-b">Preview</th>
                  <th className="py-3 px-4 border-b">Action</th>
                </tr>
              </thead>
              <tbody className="text-gray-700 text-xs sm:text-sm">
                {fonts.length > 0 ? (
                  fonts.map((font) => {
                    const name = font.name.split('.')[0]; // Use the name part before the extension
                    return (
                      <tr key={font.id} className="hover:bg-zinc-50">
                        <td className="py-3 px-4 border-b">
                          {font.name}
                        </td>
                        <td className="py-3 px-4 border-b my-font" style={{ fontFamily: `${name}` }}>
                          Example Style
                        </td>
                        <td className="py-3 px-4 border-b text-red-600 cursor-pointer">
                          <button onClick={() => handleDeleteFont(font.id)}>Delete</button>
                        </td>
                      </tr>
                    );
                  })
                ) : (
                  <tr>
                    <td colSpan="3" className="py-3 px-4 border-b text-center">No fonts available</td>
                  </tr>
                )}
              </tbody>
            </table>
          </div>
        </div>
    }


  </>

  );
}
