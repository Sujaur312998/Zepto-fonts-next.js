'use client';
import axios from 'axios'
import React, { useEffect, useState } from 'react';
import TextField from '@mui/material/TextField';
import { styled } from '@mui/material/styles';
import { host } from '@/app/host';
import Loader from "@/app/Loader";
import { useRouter, useSearchParams } from 'next/navigation';

const CustomTextField = styled(TextField)(({ theme }) => ({
    '& .MuiInputBase-root': {
        height: 35,
        padding: '5px',
    }
}));


const CreateGroup = () => {
    const font_group = {
        font_name: '',
        font_title: '',
        specific_size: 1.00,
        price_change: 0
    }

    const [isLoading, setIsLoading] = useState(false)
    const [update, setUpdate] = useState(false)
    const [groupTitle, setGroupTitle] = useState('')
    const [fontGroup, setFontGroup] = useState([font_group])
    const [fonts, setFonts] = useState([])

    const router = useRouter()
    const searchParams = useSearchParams();
    const encodedObj = searchParams.get('data');

    useEffect(()=>{
        if(encodedObj){
            setUpdate(true)
            const obj = JSON.parse(decodeURIComponent(encodedObj));
            setGroupTitle(obj.groupTitle);
            setFontGroup(obj.fontGroup);
            router.replace('/create_group');
        }
    },[encodedObj])

    // Fetch fonts from the server
    const fetchFonts = () => {
        setIsLoading(true)
        axios.get(`${host}/fontFile.php`)
            .then(response => {
                setIsLoading(false)
                setFonts(response.data.fonts || []);
            })
            .catch((error) => {
                setIsLoading(false)
                console.log(error);
            });
    };

    useEffect(() => {
        fetchFonts();
    }, []);

    const handleAddRow = () => {
        const list = [...fontGroup]
        list.push(font_group)
        setFontGroup(list)
    }

    const handleDelete = (index, item) => {
        setIsLoading(true)
        if (update) {
            axios.post(`${host}/delete_font_onUpdate.php`, { fontGroup: item.fontGroup }, {
                headers: {
                    'Content-Type': 'application/json',
                }
            }).then(response => {
                setIsLoading(false)
                console.log(response.data);
            }).catch(error => {
                setIsLoading(false)
                console.log(error);
            })

        }
        const list = [...fontGroup]
        if (list.length > 1) {
            list.splice(index, 1);
            setFontGroup(list);
        }



    }

    const handleCreate = () => {
        setIsLoading(true)
        if (fontGroup.length > 1) {
            const data = {
                groupTitle,
                fontGroup,
            };

            axios.post(`${host}/create_font_Groups.php`, data, {
                headers: {
                    'Content-Type': 'application/json',
                }
            }).then(response => {
                setIsLoading(false)
                alert(" Successfully Added!!!")
                console.log(response.data);
            }).catch(error => {
                setIsLoading(false)
                console.log(error);
            })
        }
    }

    const handleUpdate = () => {
        setIsLoading(true)
        const data = {
            groupTitle,
            fontGroup,
        };
        axios.post(`${host}/update_font_groups.php`, data, {
            headers: {
                'Content-Type': 'application/json',
            }
        }).then(response => {
            router.push('/font_groups')
            console.log(response.data);
        }).catch(error => {
            setIsLoading(false)
            console.log(error);
        })

    }

    const hanndleChanges = (e, index) => {
        const { name, value } = e.target
        const list = [...fontGroup]
        list[index] = {
            ...list[index],
            [name]: value
        };
        setFontGroup(list)
    }

    return (<>

        {isLoading ? <Loader /> :
            <div className="mx-auto w-[80%] my-5 p-6  ">
                <h2 className="text-2xl font-semibold text-gray-800 mb-2">{update ? 'Update Font Group' : 'Create Font Group'}</h2>
                <p className="text-sm text-gray-500 mb-4">You have to select at least two fonts</p>
                <input
                    type="text"
                    value={groupTitle}
                    onChange={(e) => setGroupTitle(e.target.value)}
                    placeholder="Group Title"
                    className="w-full px-4 py-1 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-black"
                />
                <ul className=''>
                    {
                        fontGroup?.map((item, index) => {
                            return (
                                <li key={index} className='flex items-center justify-center bg-white shadow-lg rounded-lg my-3 px-5 '>
                                    <div className='grid grid-cols-12 my-3 p-1 gap-2'>

                                        {/* Font Name */}
                                        <input
                                            name='font_name'
                                            type="text"
                                            placeholder="Font Name"
                                            value={item.font_name}
                                            onChange={(e) => hanndleChanges(e, index)}
                                            className="w-full px-4 py-1 col-span-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-black"
                                        />
                                        {/* Select Font */}
                                        <select
                                            name='font_title'
                                            value={item.font_title}
                                            onChange={(e) => hanndleChanges(e, index)}
                                            className='w-full px-4 py-1 col-span-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-black'>
                                            <option value="" hidden >Select a Font</option>
                                            {
                                                fonts?.map(font => {
                                                    return <option key={font.id} value={font.id}>{font.name}</option>
                                                })
                                            }

                                        </select>

                                        {/* Specific Size */}
                                        <CustomTextField
                                            name='specific_size'
                                            label="Specific Size"
                                            type="number"
                                            variant="outlined"
                                            className="col-span-3"
                                            value={item.specific_size}
                                            onChange={(e) => hanndleChanges(e, index)}
                                            slotProps={{
                                                inputLabel: {
                                                    shrink: true,
                                                },
                                            }}
                                        />

                                        {/* Price Change */}
                                        <CustomTextField
                                            name='price_change'
                                            label="Price Change"
                                            type="number"
                                            variant="outlined"
                                            className="col-span-3"
                                            value={item.price_change}
                                            onChange={(e) => hanndleChanges(e, index)}
                                            slotProps={{
                                                inputLabel: {
                                                    shrink: true,
                                                },
                                            }}
                                        />

                                    </div>
                                    {fontGroup?.length > 1 ?
                                        <button
                                            onClick={() => handleDelete(index, item)}
                                            className='pl-2 text-red-600'
                                        >
                                            X
                                        </button> : null
                                    }


                                </li>

                            )
                        })
                    }
                </ul>


                <div className='flex justify-between mt-6'>
                    <button
                        onClick={handleAddRow}
                        className="px-2 py-1 text-gray-900 rounded-md shadow-md  focus:outline-none focus:ring-1 focus:ring-black border-2 "
                    >
                        + Add Row
                    </button>
                    <button
                        onClick={update ? handleUpdate : handleCreate}
                        disabled={fontGroup?.length > 1 ? false : true}
                        className="px-4 py-1 bg-green-500 text-white rounded-md shadow-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-300 transition duration-300"
                    >
                        {update ? "Update" : "Create"}
                    </button>
                </div>

            </div>
        }
    </>

    )

}


export default CreateGroup