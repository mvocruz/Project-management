import React, { useState } from "react";
import { makeStyles } from "@material-ui/core/styles";
import Table from "@material-ui/core/Table";
import TableBody from "@material-ui/core/TableBody";
import TableCell from "@material-ui/core/TableCell";
import TableContainer from "@material-ui/core/TableContainer";
import TableHead from "@material-ui/core/TableHead";
import TableRow from "@material-ui/core/TableRow";
import Paper from "@material-ui/core/Paper";
import axios from "axios";
import Moment from 'react-moment';

const useStyles = makeStyles({
    table: {
        minWidth: 650
    }
});

export default function BasicTable(props) {
    const classes = useStyles();
    const [rows, getRows] = useState([]);
    axios.get(`/api/projects/${props.project_id}/comment`).then(res => {
        getRows(res.data);
    });

    return (
        <TableContainer component={Paper}>
            <Table className={classes.table} aria-label="simple table">
                <TableHead>
                    <TableRow>
                        <TableCell>Posted</TableCell>
                        <TableCell>User Name</TableCell>
                        <TableCell>Email</TableCell>
                        <TableCell>Comment</TableCell>
                    </TableRow>
                </TableHead>
                <TableBody>
                    {rows.map(row =>{

                    return (
                        <TableRow key={row.id}>
                            <TableCell width="20%" ><Moment fromNow>{row.created_at}</Moment></TableCell>
                            <TableCell>{`${row.user_first_name} ${row.user_last_name}`}</TableCell>
                            <TableCell>{row.user_email}</TableCell>
                            <TableCell>{row.comment}</TableCell>
                        </TableRow>
                    )})}
                </TableBody>
            </Table>
        </TableContainer>
    );
}
