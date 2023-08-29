import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import React from 'react';
import { Head } from '@inertiajs/react';
import { Button } from 'primereact/button';
import { Column } from 'primereact/column';
import { DataTable } from 'primereact/datatable'
import { router } from '@inertiajs/react'

export default function Dashboard({ auth, displayMessages, totalFollowers }) {
    const pageLeft = () => {
        if (displayMessages.prev_page_url) {
            router.visit(displayMessages.prev_page_url, { method: 'get' });
        }
    };

    const pageRight = () => {
        if (displayMessages.next_page_url) {
            router.visit(displayMessages.next_page_url, { method: 'get' });
        }
    };

    const paginatorLeft = <Button type="button" onClick={pageLeft} label='Next 100' />;
    const paginatorRight = <Button type="button" onClick={pageRight} label='Previous 100' />;

//     const summaryTable = (
//         <DataTable value={displayMessages.data} paginator rows={10} rowsPerPageOptions={[10, 20, 50, 100]} tableStyle={{ minWidth: '50rem' }}
//             paginatorLeft={paginatorLeft} paginatorRight={paginatorRight} >
//             <Column field="totalFollowers" header="Total number of followers gained in the past 30 days
//  " />
//         </DataTable>
//     )

    const messageTable = (
        <DataTable value={displayMessages.data} paginator rows={10} rowsPerPageOptions={[10, 20, 50, 100]} tableStyle={{ minWidth: '50rem' }}
            paginatorLeft={paginatorLeft} paginatorRight={paginatorRight} >
            <Column field="formattedMessage" header="Message" />
        </DataTable>
    )

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>}
        >
            <Head title="Dashboard" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    {messageTable}
                </div>
                <p>
                    {totalFollowers}
                </p>
            </div>
        </AuthenticatedLayout>
    );
}
